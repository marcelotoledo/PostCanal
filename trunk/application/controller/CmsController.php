<?php

/**
 * CMS controller class
 * 
 * @category    Blotomate
 * @package     Controller
 */
class CmsController extends SessionController
{
    /**
     * url response constants 
     */
    const URL_OK        = "url_ok";
    const URL_FAILED    = "url_failed";
    const URL_ERROR_3XX = "url_error_3xx";
    const URL_ERROR_4XX = "url_error_4xx";
    const URL_ERROR_5XX = "url_error_5xx";

    /**
     * CMS type response constants
     */
    const CMS_TYPE_OK          = "cms_type_ok";
    const CMS_TYPE_FAILED      = "cms_type_failed";
    const CMS_TYPE_MAINTENANCE = "cms_type_maintenance";


    /**
     * CMS controller constructor
     *
     * @param   AB_Request  $request
     * @param   AB_Response $response
     * @return  void
     */
    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
        $this->sessionAuthorize();
    }

    /**
     * Add action
     *
     * @return void
     */
    public function addAction()
    {
        $this->setViewLayout('dashboard');
        $this->setViewParameter('cms', new UserCMS());
    }

    /**
     * Check URL base action
     *
     * @return void
     */
    public function checkUrlBaseAction()
    {
        $this->setViewLayout(null);
        $this->setViewTemplate(null);
        
        $url_status       = null;
        $url              = $this->getRequestParameter('url');
        $cms_type_status  = self::CMS_TYPE_FAILED;
        $cms_type         = null;
        $cms_type_name    = "";
        $cms_type_version = "";
        $url_admin_status = self::URL_FAILED;
        $url_admin        = "";


        /* fix url */

        if(!eregi("^[[:alpha:]]+:\/\/", $url)) $url = "http://" . $url;
        if(eregi("[[:alpha:]]\/$", $url)) $url = substr($url, 0, -1);


        /* get response from url */

        $response = null;

        if(!empty($url))
        {
            try
            {
                $client = new Zend_Http_Client($url);
                $response = $client->request();
            }
            catch(Exception $exception)
            {
                $message = "failed to get response from (" . $url . "); ";
                $message.= $exception->getMessage();
                AB_Log::write($message, AB_Log::PRIORITY_INFO);
            }
        }

    
        /* get url status from response */

        $url_status = self::getUrlStatusFromResponse($response);


        /* get cms type */

        if(is_object($response) && $url_status == self::URL_OK)
        {
            /* discovery CMS type from URL */

            $cms_type = self::discoveryCMSTypeFromURL($url);

            /* discovery CMS type from headers */

            if(!is_object($cms_type))
            {
                $cms_type = self::discoveryCMSTypeFromHeaders(
                    $response->getHeaders());
            }

            /* discovery CMS type from body */

            if(!is_object($cms_type))
            {
                $cms_type = self::discoveryCMSTypeFromHTML(
                    $response->getBody());
            }
        }

        if(is_object($cms_type))
        {
            $cms_type_status = self::CMS_TYPE_OK;

            if($cms_type->maintenance == true)
            {
                $cms_type_status = self::CMS_TYPE_MAINTENANCE;
            }

            $cms_type_name = $cms_type->name;
            $cms_type_version = $cms_type->version;

            /* get url admin */

            $cms_type->getHandler(); // ...TODO 
        }


        $this->setViewData(Zend_Json::encode(array(
            'url_status'       => $url_status,
            'url'              => $url,
            'cms_type_status'  => $cms_type_status,
            'cms_type_name'    => $cms_type_name,
            'cms_type_version' => $cms_type_version,
            'url_admin_status' => $url_admin_status,
            'url_admin'        => $url_admin
        )));
    }

    /**
     * Get URL status from response
     *
     * @param   Zend_Http_Response  $response
     * @return  string
     */
    private function getUrlStatusFromResponse($response)
    {
        $status = null;
        $result = self::URL_FAILED;

        if(is_object($response))
        {
            $status = $response->getStatus();
        }

        if($status == 200)
        {
            $result = self::URL_OK;
        }
        elseif($status >= 300 && $status < 400)
        {
            $result = self::URL_ERROR_3XX;
        }
        elseif($status >= 400 && $status < 500)
        {
            $result = self::URL_ERROR_4XX;
        }
        elseif($status >= 500 && $status < 600)
        {
            $result = self::URL_ERROR_5XX;
        }

        return $result;
    }

    /**
     * Discovery CMS type from url
     *
     * @param   string          $url
     * @return  CMSType|null
     */
    private function discoveryCMSTypeFromURL($url)
    {
        $cms_type = null;

        /* test url value */

        $discovery = current(CMSTypeDiscovery::findByNameValue(
            CMSTypeDiscovery::NAME_URL,
            $url, true
        ));

        if(is_object($discovery))
        {
            $cms_type = CMSType::findByPrimaryKey($discovery->cms_type_id);
        }

        return $cms_type;
    }

    /**
     * Discovery CMS type from Headers
     *
     * @param   array           $headers
     * @return  CMSType|null
     */
    private function discoveryCMSTypeFromHeaders($headers)
    {
        $cms_type = null;
        $results = array();

        foreach($headers as $name => $value)
        {
            $header = strtolower($name . ": " . $value);

            /* test header value */

            $discovery = CMSTypeDiscovery::findByNameValue(
                CMSTypeDiscovery::NAME_HEADER,
                $header, true
            );

            for($i=0; $i<count($discovery); $i++)
            {
                $cms_type_id = $discovery[$i]->cms_type_id;

                array_key_exists($cms_type_id, $results) ? 
                    $results[$cms_type_id]++             :
                    $results[$cms_type_id] = 1           ;
            }
        }

        /* top ponctuated cms type come first */

        arsort($results);
        $cms_type_id = key($results);

        if(!empty($cms_type_id))
        {
            $cms_type = CMSType::findByPrimaryKey($cms_type_id);
        }

        return $cms_type;
    }

    /**
     * Discovery CMS type from HTML
     *
     * @param   string          $body
     * @return  CMSType|null
     */
    private function discoveryCMSTypeFromHTML($body)
    {
        $cms_type = null;
        $results = array();

        /* get all html rules */

        $discovery = CMSTypeDiscovery::findByNameValue(
            CMSTypeDiscovery::NAME_HTML
        );

        /* clean body spaces and newlines */

        $body = ereg_replace("\n", "", $body);
        $body = ereg_replace("[[:space:]]+", " ", $body);

        /* test html */

        for($i=0; $i<count($discovery); $i++)
        {
            $cms_type_id = $discovery[$i]->cms_type_id;
            $query = $discovery[$i]->value;

            if(eregi($query, $body) > 0)
            {
                array_key_exists($cms_type_id, $results) ? 
                    $results[$cms_type_id]++             :
                    $results[$cms_type_id] = 1           ;
            }
        }

        /* top ponctuated cms type come first */

        arsort($results);
        $cms_type_id = key($results);

        if(!empty($cms_type_id))
        {
            $cms_type = CMSType::findByPrimaryKey($cms_type_id);
        }

        return $cms_type;
    }
}
