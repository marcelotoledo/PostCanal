<?php

/**
 * Abstract controller class
 * 
 * @category    Blotomate
 * @package     Controller
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
abstract class AbstractController extends AB_Controller
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
        /* session start */

        Zend_Session::start();

        parent::__construct($request, $response);
    }

    /**
     * Run controller action
     *
     * @param   string      $name   Action name
     * @throws  AB_Exception
     * @return  void
     */

    public function runAction($name=null)
    {
        try
        {
            parent::runAction($name);
        }
        catch(AB_Exception $exception)
        {
            /* add user profile information to exception */

            $message = "an error occurred while trying to execute action";

            $data = ($id = intval($this->user_profile_id) > 0) ?
                array('user_profile_id' => $id) : 
                array();

            AB_Exception::forward($message, E_USER_NOTICE, $exception, $data);
        }
    }   

    /* JSON */

    /**
     * Set view data encoded on Json format
     *
     * @param   mixed   $data
     * @return  void
     */
    public function setViewDataJson($data)
    {
        parent::setViewData(Zend_Json::encode($data));
    }

    /* SESSION */

    /**
     * Get overloading
     * Get session attribute
     *
     * @param   string  $name
     * @return  mixed
     */
    protected function __get($name)
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
     * Set session attribute
     *
     * @param   string  $name
     * @param   mixed   $value
     * @return  void
     */
    protected function __set($name, $value)
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
    protected function sessionCreate()
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
     * @return  boolean
     */
    protected function sessionAuthorize()
    {
        $registry = AB_Registry::singleton();
        $redirect = $registry->session->unauthorized->redirect;
        $alive = self::sessionAlive();

        if($alive == false)
        {
            $this->setResponseRedirect($redirect, AB_Response::STATUS_UNAUTHORIZED);
            $message = "session unauthorized";
            $data = array('method' => __METHOD__);
            throw new AB_Exception($message, E_USER_NOTICE, $data);
        }

        return $alive;
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
