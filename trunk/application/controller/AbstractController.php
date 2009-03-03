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
     * @var string
     */
    protected $session = null;

    /**
     * Session name
     *
     * @var string
     */
    protected static $session_name = 'application';

    /**
     * Translation
     *
     * @var string
     */
    protected $translation = null;


    /**
     * Base controller constructor
     *
     * @see AB_Controller::__construct
     */
    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
        $this->__session();
        $this->__translation();
    }

    /**
     * Session initializer
     *
     * @return  void
     */
    protected function __session()
    {
        $this->session = new AB_Session(self::$session_name);
        $registry = AB_Registry::singleton();
        $registry->session->object = $this->session;
    }

    /**
     * Translation initializer
     *
     * @return  void
     */
    protected function __translation()
    {
        $registry = AB_Registry::singleton();
        $culture = $registry->translation->culture;
        $this->translation = new AB_Translation($culture);
        $template = $registry->view->template;
        $this->translation->load($template);
        $registry->translation->object = $this->translation;
    }

    /**
     * Run controller action
     *
     * @see AB_Controller::runAction
     */
    public function runAction($name)
    {
        try
        {
            parent::runAction($name);
        }
        catch(AB_Exception $exception)
        {
            /* add user profile information to exception */

            $id = intval($this->session->user_profile_id);
            $_m = "an error occurred during the execution of action (" . $name . ")";
            $_d = array('method' => __METHOD__);
            if($id > 0) $_d = array_merge($_d, array('user_profile_id' => $id));
            AB_Exception::forward($_m, E_USER_NOTICE, $exception, $_d);
        }
    }   

    /**
     * Check login session
     *
     * @return  boolean
     */
    protected function sessionAuthorize()
    {
        if(($active = $this->session->getActive()) == false)
        {
            $registry = AB_Registry::singleton();
            $item = $registry->session->unauthorized->redirect;
            $redirect = empty($item) ? BASE_URL : $item;

            $this->setResponseRedirect($redirect, AB_Response::STATUS_UNAUTHORIZED);
            $_m = "session unauthorized";
            $_d = array('method' => __METHOD__);
            throw new AB_Exception($_m, E_USER_NOTICE, $_d);
        }

        return $active;
    }
}
