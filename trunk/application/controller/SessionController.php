<?php

/**
 * Session controller class
 * 
 * @category    Autoblog
 * @package     Controller
 */
class SessionController extends AB_Controller
{
    /**
     * Base controller constructor
     *
     * @param   AB_Request  $request
     * @param   AB_Response $response
     * @return void
     */
    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
    }

    /**
     * Action magic method
     *
     * @param   string  $method
     * @param   array   $arguments
     * @return  void
     */
    public function __call($method, $arguments)
    {
        if(strpos($method, 'Action') > 0)
        {
            $this->getResponse()->setRedirect(BASE_URL);
        }
    }

    /**
     * Create login session
     *
     * @param   string  $identification
     * @return  void
     */
    public function sessionCreate ($identification)
    {
        $registry = AB_Registry::singleton();
        $namespace = $registry->session->namespace;
        $expiration = $registry->session->expiration;

        $login = new Zend_Session_Namespace($namespace);
        $login->identification = $identification;
        $login->setExpirationSeconds($expiration);
        $login->lock();
    }

    /**
     * Get session identification
     *
     * @return  mixed
     */
    public static function getSessionIdentification()
    {
        $registry = AB_Registry::singleton();
        $namespace = $registry->session->namespace;
        $login = new Zend_Session_Namespace($namespace);

        return $login->identification;
    }

    /**
     * Check session status
     *
     * @return boolean
     */
    public static function sessionStatus()
    {
        $registry = AB_Registry::singleton();
        $namespace = $registry->session->namespace;

        $login = new Zend_Session_Namespace($namespace);
        $status = false;

        if(is_object($login))
        {
            if(!empty($login->identification)) $status = true;
        }

        return $status;
    }

    /**
     * Check login session
     *
     * @return  void
     */
    public function sessionCheck()
    {
        $registry = AB_Registry::singleton();
        $redirect = $registry->session->unauthorized->redirect;

        if(self::sessionStatus() == false)
        {
            $this->getResponse()->setRedirect(
                $redirect, AB_Response::STATUS_UNAUTHORIZED);
        }
    }

    /**
     * Destroy login session
     *
     * @param   string  $login
     * @param   string  $password
     * @return  void
     */
    function sessionDestroy()
    {
        $registry = AB_Registry::singleton();
        $namespace = $registry->session->namespace;

        $login = new Zend_Session_Namespace($namespace);
        $login->unLock();
        $login->unsetAll();
    }
}
