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

        /* log requested url */

        $message = "url (" . $url . ") requested by ";

        foreach(array('HTTP_CLIENT_IP', 
                      'HTTP_X_FORWARDED_FOR', 
                      'REMOTE_ADDR') as $i)
        {
            if(array_key_exists($i, $_SERVER))
            {
                $message.= strtolower($i) . " (" . $_SERVER[$i] . ") ";
            }
        }

        if(!empty($this->user_profile_id)) # TODO bugfix
        {
            $message.= "user profile (" . $this->user_profile_id . ")";
        }

        AB_Log::write($message, 
                      E_USER_NOTICE,
                      $this->getRequestController(),
                      $this->getRequestAction());

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

            # $info = $cms_type->getPluginInfo($url);

            # /* get url admin */

            # if(array_key_exists('url_admin', $info))
            # {
                # $url_admin = $info['url_admin'];
            # }

            # /* get response from url_admin */

            # $client->request($url_admin);
            # $url_admin_status = $client->getStatus();
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
}
