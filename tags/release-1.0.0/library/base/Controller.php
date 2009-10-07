<?php

/**
 * Base Controller
 * 
 * @category    PostCanal
 * @package     Base Library
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class B_Controller
{
    /**
     * View
     *
     * @var B_View
     */
    public $view;

 
    /**
     * Access to data
     * 
     * @param   string  $name
     * @return  mixed
     */
    public function __call ($name, $arguments)
    {
        if($name == "view") return $this->view;
        else                return B_Registry::get($name . '/object');
    }

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
        return method_exists($this, ('A_' . $name));
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

        if($this->response()->isXML() == true)
        {
            $this->view()->setLayout(null);
            $this->view()->setTemplate(null);
        }

        /* render only for non redirect request */

        if($this->response()->isRedirect() == false)
        {
            ob_start();
            $this->view()->render();
            $this->response()->setBody(ob_get_clean());
        }
    }

    /**
     * Session authorize
     *
     * @return  boolean
     */
    protected function authorize($redirect=null)
    {
        if(($active = $this->session()->getActive()) == false)
        {
            if($redirect == null)
            {
                $redirect = BASE_URL;
            }

            $this->response()->setRedirect($redirect, B_Response::STATUS_UNAUTHORIZED);
            $_m = "session unauthorized";
            $_d = array('method' => __METHOD__);
            throw new B_Exception($_m, E_NOTICE, $_d);
        }

        return $active;
    }
}
