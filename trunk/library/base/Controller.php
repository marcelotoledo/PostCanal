<?php

/**
 * Controller
 * 
 * @category    Blotomate
 * @package     Base
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
class B_Controller
{
    /**
     * Registry
     *
     * @var B_Registry
     */
    public $registry;

    /**
     * View
     *
     * @var B_View
     */
    public $view;

 
    /**
     * Access to registry data
     * 
     * @param   string  $name
     * @return  mixed
     */
    public function __get ($name)
    {
        return $this->registry->{$name}->object;
    }

    public function __set ($name, $value) { } // read-only

    /**
     * Before action
     */
    public function before()
    {
    }

    /**
     * After action
     */
    public function after()
    {
    }

    /**
     * Check action
     * 
     * @param   string      $name   Action name
     * @return  boolean
     */
    public function check($name)
    {
        return is_callable(array($this, ("A_" . $name)));
    }

    /**
     * Run controller action
     * 
     * @param   string      $name
     * @return  void
     */
    public function run($name)
    {
        $this->{('A_' . $name)}();

        /* unset layout and template for xml response */

        if($this->response->isXML() == true)
        {
            $this->view->setLayout(null);
            $this->view->setTemplate(null);
        }

        /* render only for non redirect request */

        if($this->response->isRedirect() == false)
        {
            ob_start();
            $this->view->render();
            $this->response->setBody(ob_get_clean());
        }
    }

    /**
     * Session authorize
     *
     * @return  boolean
     */
    protected function authorize($redirect=null)
    {
        if(($active = $this->session->getActive()) == false)
        {
            if($redirect == null)
            {
                $redirect = $this->registry->session->unauthorized->redirect;
                if(isset($redirect) == false) $redirect = BASE_URL;
            }

            $this->response->setRedirect($redirect, B_Response::STATUS_UNAUTHORIZED);
            $_m = "session unauthorized";
            $_d = array('method' => __METHOD__);
            throw new B_Exception($_m, E_USER_NOTICE, $_d);
        }

        return $active;
    }
}