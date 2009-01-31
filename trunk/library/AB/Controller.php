<?php

/**
 * Controller
 * 
 * @category    Blotomate
 * @package     AB
 */
class AB_Controller
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
     * Get request parameter
     *
     * @param   string  $name
     * @return  void
     */
    public function getRequestParameter($name)
    {
        return $this->request->{$name};
    }

    /**
     * Set view layout
     *
     * @param   string  $layout
     * @return  void
     */
    public function setViewLayout($layout)
    {
        $this->view->setLayout($layout);
    }

    /**
     * Set view template
     *
     * @param   string  $template
     * @return  void
     */
    public function setViewTemplate($template)
    {
        $this->view->setTemplate($template);
    }

    /**
     * Set view parameter
     *
     * @param   string  $name
     * @param   mixed   $value
     * @return  void
     */
    public function setViewParameter($name, $value)
    {
        $this->view->{$name} = $value;
    }

    /**
     * Set view data (override parameters)
     *
     * @param   string  $value
     * @return  void
     */
    public function setViewData($value)
    {
        $this->view->setData($value);
    }

    /**
     * Set response status
     *
     * @return  void
     */
    public function setResponseStatus($status)
    {
        $this->response->setStatus($status);
    }

    /**
     * Set response redirect
     *
     * @return  void
     */
    public function setResponseRedirect($url, $status=null)
    {
        $this->response->setRedirect($url, $status);
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
            $this->{$action_method}();

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
            throw new Exception ("action " . $action . " not found");
        }
    }
}
