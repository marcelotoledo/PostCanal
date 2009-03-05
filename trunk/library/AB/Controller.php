<?php

/**
 * Controller
 * 
 * @category    Blotomate
 * @package     AB
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
class AB_Controller
{
    /**
     * Registry
     *
     * @var AB_Registry
     */
    public $registry;

    /**
     * View
     *
     * @var AB_View
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
     * Check action
     * 
     * @param   string      $name   Action name
     * @return  boolean
     */
    public function checkAction($name)
    {
        return is_callable(array($this, ($name . "Action")));
    }

    /**
     * Run controller action
     * 
     * @param   string      $name
     * @return  void
     */
    public function runAction($name)
    {
        $this->{($name . "Action")}();

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
    protected function sessionAuthorize()
    {
        if(($active = $this->session->getActive()) == false)
        {
            $redirect = $this->registry->session->unauthorized->redirect;
            if(isset($redirect) == false) $redirect = BASE_URL;

            $this->response->setRedirect($redirect, AB_Response::STATUS_UNAUTHORIZED);
            $_m = "session unauthorized";
            $_d = array('method' => __METHOD__);
            throw new AB_Exception($_m, E_USER_NOTICE, $_d);
        }

        return $active;
    }
}
