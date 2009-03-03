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
     * General constants
     */
    const STATUS_OK          = "ok";
    const STATUS_FAILED      = "failed";
    const STATUS_UNKNOWN     = "unknown";
    const STATUS_MAINTENANCE = "maintenance";

    const CMS_ADD_SESSION    = "cms_add_session";


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
        $this->getRequestMethod() == AB_Request::METHOD_POST ?
            $this->addMethodPOST() :
            $this->addMethodGET();
    }

    /**
     * Add action, method GET
     *
     * @return void
     */
    private function addMethodGET()
    {
        $this->setViewLayout('dashboard');
        $this->setViewParameter('cms', new UserCMS());

        /* create new namespace to store new cms information */

        $ss = new Zend_Session_Namespace(self::CMS_ADD_SESSION);
        $ss->data = array();
    }

    /**
     * Add action, method POST (save)
     *
     * @return void
     */
    private function addMethodPOST()
    {
        $this->responseIsAjax(true);

        $result = self::STATUS_FAILED;

        $ss = new Zend_Session_Namespace(self::CMS_ADD_SESSION);
        $data = ((array) $ss->data);


        if(!array_key_exists('url', $data) ||
           !array_key_exists('manager_url', $data) ||
           !array_key_exists('cms_type', $data))
        {
            $_m = "required data is not available in session";
            $_d = array('method' => __METHOD__);
            throw new AB_Exception($_m, E_USER_NOTICE, $_d);
        }

        $cms = new UserCMS();
        $cms->user_profile_id = $this->user_profile_id;
        $cms->cms_type_id = $data['cms_type'];
        $cms->name = $this->getRequestParameter('name');
        $cms->url = $data['url'];
        $cms->manager_url = $data['manager_url'];
        $cms->manager_username = $this->getRequestParameter('username');
        $cms->manager_password = $this->getRequestParameter('password');
        $cms->status = UserCMS::STATUS_OK;

        try
        {
            $cms->save();
            $result = self::STATUS_OK;
        }
        catch(AB_Exception $exception)
        {
            $_m = "failed to add new cms";
            $_d = array('method' => __METHOD__);
            AB_Exception::forward($_m, E_USER_WARNING, $exception, $_d);
        }

        $this->setViewData(compact(array('result')));
    }

    /**
     * Check action
     *
     * @return void
     */
    public function checkAction()
    {
        $this->responseIsAjax(true);

        AB_Loader::loadApplicationLibrary("ApplicationHTTPClient");
        
        $ss = new Zend_Session_Namespace(self::CMS_ADD_SESSION);

        $url = null;
        $manager_url = null;
        $data = array();
        $client = new ApplicationHTTPClient();

        /* check url (and manager url internally) */

        if(strlen(($url = $this->getRequestParameter('url'))) > 0)
        {
            $data = $this->checkURL($url, $client);
            $ss->data = array_merge($ss->data, $data);
        }

        /* manager url */

        if(strlen(($manager_url = $this->getRequestParameter('manager'))) > 0)
        {
            $data = ((array) $ss->data);

            if(!array_key_exists('cms_type', $data))
            {
                $_m = "cms type is not available in session";
                $_d = array('method' => __METHOD__);
                throw new AB_Exception($_m, E_USER_NOTICE, $_d);
            }

            $type = CMSType::findByPrimaryKey($data['cms_type']);
            $data = $this->checkManagerURL($manager_url, $client, $type);
            $ss->data = array_merge($ss->data, $data);
        }

        /* manager login */

        if(strlen(($username = $this->getRequestParameter('username'))) > 0 &&
           strlen(($password = $this->getRequestParameter('password'))) > 0)
        {
            $data = ((array) $ss->data);

            if(!array_key_exists('cms_type', $data) ||
               !array_key_exists('manager_url', $data))
            {
                $_m = "required data is not available in session";
                $_d = array('method' => __METHOD__);
                throw new AB_Exception($_m, E_USER_NOTICE, $_d);
            }

            $data = $this->checkLogin($username, $password, $data);
            $ss->data = array_merge($ss->data, $data);
        }

        /* send response */

        $this->setViewData($data);
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

        $cms_type         = 0;
        $cms_type_status  = self::STATUS_FAILED;
        $cms_type_name    = "";
        $cms_type_version = "";

        $manager_url      = "";
        $manager_status   = self::STATUS_FAILED;

        /* http request */

        try { $client->request($url); } catch(AB_Exception $e) { }

        /* log requested url */

        $_m = "url (" . $url . ") requested by ";
        $_d = array('method' => __METHOD__);

        foreach(array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR') as $i)
        {
            if(array_key_exists($i, $_SERVER))
            {
                $_m .= strtolower($i) . " (" . $_SERVER[$i] . ") ";
            }
        }

        AB_Log::write($_m, E_USER_NOTICE, $_d);

        /* discovery cms type */

        $url_status = $client->getStatus();
        $type = null;

        if($url_status == ApplicationHTTPClient::STATUS_OK)
        {
            $type = CMSType::discovery($url, $client->getHeaders(), $client->getBody());
            $cms_type_status = is_object($type) ? self::STATUS_OK : self::STATUS_UNKNOWN;
        }

        if($cms_type_status == self::STATUS_OK)
        {
            $cms_type = $type->cms_type_id;
            $cms_type_status = self::STATUS_OK;

            if($type->maintenance == true) $cms_type_status = self::STATUS_MAINTENANCE;

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

                if($manager_status != self::STATUS_OK)
                {
                    $message = "default manager url (" . $manager_url . ") " .
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
        $manager_status = self::STATUS_FAILED;

        /* get response from manager url */

        try { $client->request($manager_url); } catch(AB_Exception $e) { }
        $url_status = $client->getStatus();

        /* check manager response */

        if($url_status == ApplicationHTTPClient::STATUS_OK)
        {
            if(CMSType::managerCheckHTML(
                $client->getBody(), $type->getConfiguration()) == true)
            {
                $manager_status = self::STATUS_OK;
            }
        }

        return compact(array('manager_url', 'manager_status'));
    }

    /**
     * Check manager login
     *
     * @params  string  $username
     * @params  string  $password
     * @param   array   $data
     * @return  string
     */
    private function checkLogin($username, $password, $data)
    {
        $login_status = self::STATUS_FAILED;

        return compact(array('login_status'));
    }
}
