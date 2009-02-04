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
        include APPLICATION_PATH . "/library/ApplicationHTTPClient.php";

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

        $url = ApplicationHTTPClient::fixURL($url);

        /* http request */

        $client = new ApplicationHTTPClient();
        $client->request($url);
        $url_status = $client->getStatus();

        /* get cms type */

        if($url_status == ApplicationHTTPClient::STATUS_OK)
        {
            /* discovery CMS type from URL */

            $cms_type = self::discoveryCMSTypeFromURL($url);

            /* discovery CMS type from headers */

            if(!is_object($cms_type))
            {
                $cms_type = self::discoveryCMSTypeFromHeaders(
                    $client->getHeaders());
            }

            /* discovery CMS type from body */

            if(!is_object($cms_type))
            {
                $cms_type = self::discoveryCMSTypeFromHTML(
                    $client->getBody());
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

            $handler = $cms_type->getHandler();
            $handler->setBaseURL($url);
            $url_admin = $handler->getAdminURL();

            /* get response from url_admin */

            $client->request($url_admin);
            $url_admin_status = $client->getStatus();
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
    private function discoveryCMSTypeFromHTML($html)
    {
        $cms_type = null;
        $results = array();

        /* get all html rules */

        $discovery = CMSTypeDiscovery::findByNameValue(
            CMSTypeDiscovery::NAME_HTML
        );

        /* clean html */

        $html = ApplicationHTTPClient::cleanHTML($html);

        /* test html */

        for($i=0; $i<count($discovery); $i++)
        {
            $cms_type_id = $discovery[$i]->cms_type_id;
            $query = $discovery[$i]->value;

            if(eregi($query, $html) > 0)
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
