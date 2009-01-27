<?php

/**
 * Error controller class
 * 
 * @category    Blotomate
 * @package     Controller
 */

class ErrorController extends AB_Controller
{
    /**
     * Error controller constructor
     *
     * @param   AB_Request  $request
     * @param   AB_Response $response
     * @return void
     */
    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
        $this->getView()->setLayout(null);
    }

    /**
     * Status 404 action
     *
     * @return  string
     */
    public function status404Action()
    {
        return "<h1>404 Not Found</h1>";
    }

    /**
     * Status 500 action
     *
     * @return  string
     */
    public function status500Action()
    {
        return "<h1>500 Error</h1>";
    }
}
