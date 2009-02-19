<?php

/**
 * CMS controller class
 * 
 * @category    Blotomate
 * @package     Controller
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
class CmsController extends AbstractController
{
    /**
     * CMS type status response constants
     */
    const CMS_TYPE_OK          = "ok";
    const CMS_TYPE_FAILED      = "failed";
    const CMS_TYPE_MAINTENANCE = "maintenance";

    /**
     * Manager status response constants
     */
    const MANAGER_STATUS_OK     = "ok";
    const MANAGER_STATUS_FAILED = "failed";


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
        
        $url = null;
        $manager_url = null;
        $data = array();
        $client = new ApplicationHTTPClient();

        /* check url (and manager url internally) */

        if(strlen(($url = $this->getRequestParameter('url'))) > 0)
        {
            $data = $this->checkURL($url, $client);
        }

        /* manager url */

        if(strlen(($manager_url = $this->getRequestParameter('manager'))) > 0)
        {
            $type = CMSType::findByPrimaryKey($this->getRequestParameter('type'));
            $data = $this->checkManagerURL($manager_url, $client, $type);
        }

        /* send response */

        $this->setViewDataJson($data);
    }

    /**
     * Check URL
     *
     * @params  string                  $url
     * @params  ApplicationHTTPClient   $client
     * @return  array
     */
    private function checkURL(&$url, $client)
    {
        $url_status       = null;

        $cms_type         = null;
        $cms_type_status  = self::CMS_TYPE_FAILED;
        $cms_type_name    = "";
        $cms_type_version = "";

        $manager_url      = "";
        $manager_status   = self::MANAGER_STATUS_FAILED;

        /* http request */

        $client->request($url);

        /* log requested url */

        $m = "url (" . $url . ") requested by ";

        foreach(array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR') as $i)
        {
            if(array_key_exists($i, $_SERVER))
            {
                $m .= strtolower($i) . " (" . $_SERVER[$i] . ") ";
            }
        }
    
        $a = array('method' => __METHOD__,
                   'user_profile_id' => $this->user_profile_id);

        AB_Log::write($m, E_USER_NOTICE, $a);

        /* discovery cms type */

        $url_status = $client->getStatus();
        $type = null;

        if($url_status == ApplicationHTTPClient::STATUS_OK)
        {
            $type = CMSType::discovery($url, $client->getHeaders(), $client->getBody());
        }

        if(is_object($type))
        {
            $cms_type = $type->cms_type_id;
            $cms_type_status = self::CMS_TYPE_OK;

            if($type->maintenance == true)
            {
                $cms_type_status = self::CMS_TYPE_MAINTENANCE;
            }

            $cms_type_name = $type->name;
            $cms_type_version = $type->version;
            $config = $type->getConfiguration();

            /* get manager url */

            if(array_key_exists(($k = CMSType::CONFIG_MANAGER_URL), $config))
            {
                $manager_url = ($url . $config[$k]);

                /* check manager url */

                extract($this->checkManagerURL($manager_url, $client, $type));

                /* log unexpected status for default manager url */

                if($manager_status != self::MANAGER_STATUS_OK)
                {
                    $message = "manager url (" . $manager_url . ") " .
                               "for cms type (" . $type->cms_type_id . ") " .
                               "returned a manager status (" . $manager_status . ")";
                    $a = array('method' => __METHOD__,
                                'user_profile_id' => $this->user_profile_id);
                    AB_Log::write($message, E_USER_WARNING, $a);
                }
            }
        }

        return compact(array(
            'url',
            'url_status',
            'cms_type',
            'cms_type_status',
            'cms_type_name',
            'cms_type_version',
            'manager_url',
            'manager_status'
        ));
    }

    /**
     * Check manager URL
     *
     * @params  string                  $manager_url
     * @params  ApplicationHTTPClient   $client
     * @params  CMSType                 $type
     * @return  array
     */
    private function checkManagerURL(&$manager_url, $client, $type)
    {
        $url_status     = null;
        $manager_status = self::MANAGER_STATUS_FAILED;

        /* get response from manager url */

        $client->request($manager_url);
        $url_status = $client->getStatus();

        /* check manager response */

        if($url_status == ApplicationHTTPClient::STATUS_OK)
        {
            if(CMSType::managerCheckHTML(
                $client->getBody(), $type->getConfiguration()) == true)
            {
                $manager_status = self::MANAGER_STATUS_OK;
            }
        }

        return compact(array('manager_url', 'manager_status'));
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
