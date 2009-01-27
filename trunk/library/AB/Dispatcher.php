<?php

/**
 * Application dispatcher
 * 
 * @category    Blotomate
 * @package     AB
 */
class AB_Dispatcher
{
    /**
     * Singleton instance
     * 
     * @var AB_Dispatcher
     */
    private static $instance;

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
     * Dispatcher constructor
     *
     * @return  void
     */
    private function __construct() 
    { 
        $this->request = new AB_Request();
        $this->response = new AB_Response();
    }

    private function __clone() { }

    /**
     * Singleton constructor
     * 
     * @return AB_Dispatcher
     */
    public static function singleton()
    {
        if(is_null(self::$instance) == true)
        {
            self::$instance = new self();
        }

        return self::$instance;
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
     * Dispatch request and response to controller
     *
     * @return void
     */
    public function dispatch()
    {
        $controller = null;

        try
        {
            $this->controllerFactory()->runAction();
            $this->response->send();
        }
        catch(Exception $exception)
        {
            $message = $exception->getMessage();

            /* not found exception */

            if ($this->response->getStatus() == AB_Response::STATUS_NOT_FOUND)
            {
                AB_Log::write($message, AB_Log::PRIORITY_WARNING);
            }

            /* error exception */

            else
            {
                $this->response->setStatus(AB_Response::STATUS_ERROR);
                AB_Log::write($message, AB_Log::PRIORITY_ERROR);
            }

            /* show exception in browser */

            if(empty(AB_Registry::singleton()->debug) == false &&
                     AB_Registry::singleton()->debug  == true)
            {
                $this->response->setBody($message);
            }

            /* run error controller actions */

            else
            {
                $this->controllerFactory('Error')->runAction('status' . 
                    $this->response->getStatus());
            }

            /* send response */

            $this->response->send();
        }
    }

    /**
     * Initialize controller class
     *
     * @param   string          $name   Controller name
     * @throws  Exception
     * @return  AB_Controller
     */
    private function controllerFactory($name=null)
    {
        if(empty($name))
        {
            $name = $this->request->getController();
        }

        $class_name = $name . "Controller";
        $controller = null;

        if(class_exists($class_name))
        {
            $controller = new $class_name($this->request, $this->response);
        }
        else
        {
            $this->response->setStatus(AB_Response::STATUS_NOT_FOUND);
            throw new Exception ("controller " . $name . " not found");
        }

        return $controller;
    }
}
