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
     * Request
     *
     * @var AB_Request
     */
    public $request = null;

    /**
     * Response
     *
     * @var AB_Response
     */
    public $response = null;

    /**
     * View
     * 
     * @var AB_View
     */
    public $view = null;


    /**
     * Controller constructor
     *
     * @param   AB_Request  $request
     * @param   AB_Response $response
     * @return  void
     */
    public function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;

        $request_controller = $request->getController();
        $request_action = $request->getAction();
        $view_template = $request_controller . "/" . $request_action;

        $registry = AB_Registry::singleton();
        $registry->request->controller = $request_controller;
        $registry->request->controller = $request_controller;
        $registry->view->template = $view_template;

        $this->view = new AB_View($view_template);
    }

    /**
     * Is XML
     *
     * @param   boolean     $b
     * @return  void
     */
    public function isXML($b=null)
    {
        if($b != null)
        {
            $this->response->isXML($b);
            $this->view->setLayout(null);
            $this->view->setTemplate(null);
        }

        return $this->response->isXML();
    }

    /**
     * Run controller action
     *
     * @param   string      $name   Action name
     * @throws  AB_Exception
     * @return  void
     */
    public function runAction($name)
    {
        $action = $name . "Action";

        if(is_callable(array($this, $action)) == true)
        {
            $this->{$action}();

            if($this->response->isRedirect() == false)
            {
                ob_start();
                $this->view->render();
                $this->response->setBody(ob_get_clean());
            }
        }
        else
        {
            $this->response->setStatus(AB_Response::STATUS_NOT_FOUND);
            $_m = "action (" . $action . ") not found";
            $_d = array('method' => __METHOD__);
            throw new AB_Exception($_m, E_USER_NOTICE, $_d);
        }
    }
}
