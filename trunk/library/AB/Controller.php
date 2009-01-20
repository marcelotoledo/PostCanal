<?php

/**
 * Abstract controller
 * 
 * @category    Autoblog
 * @package     AB
 */
abstract class AB_Controller
{
    /**
     * Request
     *
     * @var AB_Request
     */
    private $request;

    /**
     * Response
     *
     * @var AB_Response
     */
    private $response;

    /**
     * View
     * 
     * @var AB_View
     */
    private $view;


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
        $this->view = new AB_View($request);
    }

    /**
     * Current request
     *
     * @return  AB_Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Current response
     *
     * @return  AB_Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Current view
     *
     * @return  AB_View
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Run controller action
     *
     * @param   string      $name   Action name
     * @throws  Exception
     * @return  void
     */
    public function runAction($name=null)
    {
        $action = $name ? $name : $this->request->getAction();
        $action_method = $action . "Action";

        if(is_callable(array($this, $action_method)) == true)
        {
            $this->view->setData($this->{$action_method}());

            ob_start();
            $this->view->render();
            $this->response->setBody(ob_get_clean());
        }
        else
        {
            $this->response->setStatus(404);
            throw new Exception ("action " . $action . " not found");
        }
    }
}
