<?php

/**
 * CMS controller class
 * 
 * @category    Blotomate
 * @package     Controller
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
class C_Cms extends C_Abstract
{
    /**
     * Before action
     */
    public function before()
    {
        $this->authorize();
    }

    /**
     * Add action
     *
     * @return void
     */
    public function A_add()
    {
        $this->request->getMethod() == B_Request::METHOD_POST ?
            $this->P_add() :
            $this->G_add();
    }

    /**
     * Add action, method GET
     */
    private function G_add()
    {
        $this->view->setLayout('dashboard');
        $this->view->cms = new UserCMS();

        /* reset cms add session variables */

        $this->session->c_url = "";
        $this->session->c_url_accepted = false; 
        $this->session->c_cms_type = 0; 
        $this->session->c_cms_type_name = ""; 
        $this->session->c_cms_type_version = ""; 
        $this->session->c_cms_type_maintenance = false; 
        $this->session->c_cms_type_accepted = false; 
        $this->session->c_manager_url = "";
        $this->session->c_manager_url_accepted = false;
        $this->session->c_login_accepted = false;
        $this->session->c_publication_accepted = false;
    }

    /**
     * Add action, method POST (save)
     *
     * @return void
     */
    private function P_add()
    {
        $this->response->setXML(true);

        $added = false;

        $cms = new UserCMS();
        $cms->user_profile_id = $this->session->user_profile_id;
        $cms->cms_type_id = $this->session->c_cms_type;
        $cms->name = $this->request->name;
        $cms->url = $this->session->c_url;
        $cms->manager_url = $this->session->c_manager_url;
        $cms->manager_username = $this->request->username;
        $cms->manager_password = $this->request->password;
        $cms->status = UserCMS::STATUS_NEW;

        try
        {
            $cms->save();
            $added = true;
        }
        catch(B_Exception $exception)
        {
            $_m = "failed to add new cms";
            $_d = array('method' => __METHOD__);
            B_Exception::forward($_m, E_USER_WARNING, $exception, $_d);
        }

        $this->view->added = $added;
    }

    /**
     * Check action
     *
     * @return void
     */
    public function A_check()
    {
        $this->response->setXML(true);

        $client = new L_HTTPClient();

        /* check url (and manager url internally) */

        if(strlen(($url = $this->request->url)) > 0)
        {
            $this->checkURL($url, $client);
        }

        /* manager url */

        if(strlen(($manager_url = $this->request->manager)) > 0)
        {
            if($this->session->c_cms_type == 0)
            {
                $_m = "cms type is not available in session";
                $_d = array('method' => __METHOD__);
                throw new B_Exception($_m, E_USER_NOTICE, $_d);
            }

            $type = CMSType::findByPrimaryKey($this->session->c_cms_type);
            $this->checkManagerURL($manager_url, $client, $type);
        }

        /* manager login */

        if(strlen(($username = $this->request->username)) > 0 &&
           strlen(($password = $this->request->password)) > 0)
        {
            if($this->session->c_cms_type == 0 ||
               strlen($this->session->c_manager_url) == 0)
            {
                $_m = "cms type or manager url are not available in session";
                $_d = array('method' => __METHOD__);
                throw new B_Exception($_m, E_USER_NOTICE, $_d);
            }

            $this->checkLogin($username, $password);
        }

        /* send response */

        foreach(array(
            'url', 'url_accepted', 'cms_type_name', 'cms_type_version',
            'cms_type_accepted', 'cms_type_maintenance', 'manager_url', 
            'manager_url_accepted') as $i)
        {
            $this->view->{$i} = $this->session->{('c_' . $i)};
        }
    }

    /**
     * Check URL
     *
     * @params  string          $url
     * @params  L_HTTPClient  $client
     */
    private function checkURL(&$url, $client)
    {
        /* http request */

        try { $client->request($url); } catch(B_Exception $e) { }

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

        B_Log::write($_m, E_USER_NOTICE, $_d);

        /* discovery cms type */

        $type = null;

        if($client->getStatus() == L_HTTPClient::STATUS_OK)
        {
            $this->session->c_url_accepted = true;
            $type = CMSType::discovery($url, $client->getHeaders(), $client->getBody());
        }

        if(is_object($type))
        {
            $this->session->c_cms_type = $type->cms_type_id;
            $this->session->c_cms_type_name = $type->name;
            $this->session->c_cms_type_version = $type->version;
            $this->session->c_cms_type_maintenance = $type->maintenance;
            $this->session->c_cms_type_accepted = true;

            $config = $type->getConfiguration();

            /* get manager url */

            if(array_key_exists(($k = CMSType::CONFIG_MANAGER_URL), $config))
            {
                $manager_url = ($url . $config[$k]);

                /* check manager url */

                $this->checkManagerURL($manager_url, $client, $type);

                /* default manager url not accepted */

                $accepted = $this->session->c_manager_url_accepted;

                if($accepted == false)
                {
                    $_m = "default manager url (" . $manager_url . ") " .
                          "for cms type (" . $type->cms_type_id . ") not accepted";
                    $_d = array('method' => __METHOD__);
                    B_Log::write($_m, E_USER_WARNING, $_d);
                }
            }
        }

        $this->session->c_url = $url;
    }

    /**
     * Check manager URL
     *
     * @params  string          $manager_url
     * @params  L_HTTPClient  $client
     * @params  CMSType         $type
     */
    private function checkManagerURL(&$manager_url, $client, $type)
    {
        $this->session->c_manager_url = $manager_url;

        /* get response from manager url */

        try { $client->request($manager_url); } catch(B_Exception $e) { }

        /* check manager response */

        if($client->getStatus() == L_HTTPClient::STATUS_OK)
        {
            if(CMSType::managerCheckHTML($client->getBody(), $type->getConfiguration()))
            {
                $this->session->c_manager_url_accepted = true;
            }
        }
    }

    /**
     * Check manager login
     *
     * @params  string  $username
     * @params  string  $password
     */
    private function checkLogin($username, $password)
    {
        /* TODO */
    }

    /**
     * Check publication
     *
     * @params  string  $username
     * @params  string  $password
     */
    private function checkPublication($username, $password)
    {
        /* TODO */
    }
}
