<?php

/**
 * Session controller class
 * 
 * @category    Blotomate
 * @package     Controller
 */
class SessionController extends AB_Controller
{
    /**
     * Session
     *
     * @var Zend_Session_Namespace
     */
    private $session;


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
    protected function __call($method, $arguments)
    {
        if(strpos($method, 'Action') > 0)
        {
            $this->getResponse()->setRedirect(BASE_URL);
        }
    }

    /**
     * Get overloading
     *
     * @param   string  $name
     * @return  mixed
     */
    protected function __get ($name)
    {
        $session = $this->getSession();
        $value = null;

        if(is_object($session))
        {
            $value = $session->{$name};
        }

        return $value;
    }

    /**
     * Set overloading
     *
     * @param   string  $name
     * @param   mixed   $value
     * @return  void
     */
    protected function __set ($name, $value)
    {
        $session = $this->getSession();

        if(is_object($session))
        {
            $session->{$name} = $value;
        }
    }

    /**
     * Create login session
     *
     * @return  void
     */
    protected function sessionCreate ()
    {
        $registry = AB_Registry::singleton();
        $expiration = $registry->session->expiration;

        $session = $this->getSession();
        $session->setExpirationSeconds($expiration);
    }

    /**
     * Session lock
     *
     * @return  void
     */
    public function sessionLock()
    {
        $session = $this->getSession();
        $session->__alive__ = true;
        $session->lock();
    }

    /**
     * Check login session
     *
     * @return  void
     */
    protected function sessionAuthorize()
    {
        $registry = AB_Registry::singleton();
        $redirect = $registry->session->unauthorized->redirect;

        if(self::sessionAlive() == false)
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
    protected function sessionDestroy()
    {
        $session = $this->getSession();
        $session->unLock();
        $session->unsetAll();
        $this->session = null;
    }

    /**
     * Get session
     *
     * @return  Zend_Session_Namespace
     */
    private function getSession()
    {
        if(empty($this->session))
        {
            $this->session = self::recoverSession();
        }

        return $this->session;
    }

    /**
     * Check if session alive
     *
     * @return boolean
     */
    public static function sessionAlive()
    {
        $session = self::recoverSession();
        $alive = false;

        if(is_object($session))
        {
            $alive = ($session->__alive__ === true);
        }

        return $alive;
    }

    /**
     * Recover sesssion
     *
     * @return  Zend_Session_Namespace
     */
    public static function recoverSession()
    {
        $registry = AB_Registry::singleton();
        $namespace = $registry->session->namespace;
        return new Zend_Session_Namespace($namespace);
    }
}
