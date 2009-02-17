<?php

/**
 * CMS controller class
 * 
 * @category    Blotomate
 * @package     Controller
 */
class CmsController extends AbstractController
{
    /**
     * CMS type response constants
     */
    const CMS_TYPE_OK          = "ok";
    const CMS_TYPE_FAILED      = "failed";
    const CMS_TYPE_MAINTENANCE = "maintenance";

    /**
     * Manager html check status response constants
     */
    const M_H_STATUS_OK     = "ok";
    const M_H_STATUS_FAILED = "failed";


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
     * Check action
     *
     * @return void
     */
    public function checkAction()
    {
        include APPLICATION_PATH . "/library/ApplicationHTTPClient.php";

        $this->setViewLayout(null);
        $this->setViewTemplate(null);
        
        $url_status          = null;
        $url                 = $this->getRequestParameter('url');
        $cms_type_status     = self::CMS_TYPE_FAILED;
        $cms_type            = null;
        $cms_type_name       = "";
        $cms_type_version    = "";
        $manager_url_status  = "";
        $manager_url         = "";
        $manager_html_status = self::M_H_STATUS_FAILED;

        /* fix url */

        $url = self::fixURL($url);

        /* http request */

        $client = new ApplicationHTTPClient();
        $client->request($url);

        /* log requested url */

        $message = "url (" . $url . ") requested by ";

        foreach(array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR') as $i)
        {
            if(array_key_exists($i, $_SERVER))
            {
                $message.= strtolower($i) . " (" . $_SERVER[$i] . ") ";
            }
        }
    
        $attributes = array('method' => __METHOD__,
                            'user_profile_id' => $this->user_profile_id);

        AB_Log::write($message, E_USER_NOTICE, $attributes);

        /* discovery cms type */

        $url_status = $client->getStatus();

        if($url_status == ApplicationHTTPClient::STATUS_OK)
        {
            $cms_type = CMSType::discovery(
                $url, 
                $client->getHeaders(), 
                self::cleanHTML($client->getBody()));
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

            $info = $cms_type->getDefaultAttributes();

            /* get url admin */

            if(array_key_exists(CMSType::A_M_URL, $info))
            {
                $manager_url = $url . $info[CMSType::A_M_URL];

                /* get response from manager url */

                $client->request($manager_url);
                $manager_url_status = $client->getStatus();

                /* log status not expected */

                if($manager_url_status != ApplicationHTTPClient::STATUS_OK)
                {
                    $message = "manager url (" . $manager_url . ") " .
                               "for cms type (" . $cms_type->cms_type_id . ") " .
                               "returned a status (" . $manager_url_status . ")";
                    $_a = array('method' => __METHOD__,
                                'user_profile_id' => $this->user_profile_id);
                    AB_Log::write($message, E_USER_WARNING, $_a);
                }
            }
            else
            {
                /* this could be a lapse of "DBA" memory ! */

                $message = "cms type (" . $cms_type->cms_type_id . ") " . 
                           "not have a default attribute (" . CMSType::A_M_URL . ")";
                $_a = array('method' => __METHOD__,
                            'user_profile_id' => $this->user_profile_id);
                AB_Log::write($message, E_USER_WARNING, $_a);
            }

            /* check manager HTML from manager url response */

            if($manager_url_status == ApplicationHTTPClient::STATUS_OK)
            {
                try
                {
                    if(CMSType::managerHTMLCheck(
                        self::cleanHTML($client->getBody()), $info) == true)
                    {
                        $manager_html_status = self::M_H_STATUS_OK;
                    }
                }
                catch(AB_Exception $exception)
                {
                    $message = "manager url (" . $manager_url . ") " . 
                               "for cms type (" . $cms_type->cms_type_id . ") " .
                               "failed on (" . $exception->getMessage() . "). " .
                               "authentication will never be possible";
                    $_a = array('method' => __METHOD__,
                                'user_profile_id' => $this->user_profile_id);
                    AB_Log::write($message, E_USER_ERROR, $_a);
                }
            }
        }

        /* send response */

        $this->setViewDataJson(array(
            'url_status'          => $url_status,
            'url'                 => $url,
            'cms_type_status'     => $cms_type_status,
            'cms_type_name'       => $cms_type_name,
            'cms_type_version'    => $cms_type_version,
            'manager_url_status'  => $manager_url_status,
            'manager_url'         => $manager_url,
            'manager_html_status' => $manager_html_status
        ));
    }

    /**
     * Clean HTML (~-25%)
     * 
     * @param   string      $html
     * @return  string
     */
    private static function cleanHTML($html)
    {
        $html = ereg_replace("\r", "", $html);              // no returns
        $html = ereg_replace("\n", "", $html);              // no newlines
        $html = ereg_replace("[[:space:]]+", " ", $html);   // no spaces
        $html = ereg_replace(">[^<]*<", "><", $html);       // tags only

        return $html;
    }

    /**
     * Fix URL
     * 
     * @param   string      $url
     * @return  string
     */
    private static function fixURL($url)
    {
        $pattern = "#^(.*?//)*([\w\.\d]*)(:(\d+))*(/*)(.*)$#";
        $matches = array();
        preg_match($pattern, $url, $matches);

        $protocol = empty($matches[1]) ? "http://" : $matches[1];
        $address  = empty($matches[2]) ? ""        : $matches[2];
        $port     = empty($matches[3]) ? ""        : $matches[3];
        $resource = empty($matches[6]) ? ""        : $matches[5] . $matches[6];

        return $protocol . $address . $port . $resource;
    }
}
