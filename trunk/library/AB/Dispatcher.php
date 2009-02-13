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
     * Dispatch request and response to controller
     *
     * @return void
     */
    public function dispatch()
    {
        $has_error = false;
        $error = null; 
        $controller = $this->request->getController();
        $action = $this->request->getAction();

        try
        {
            $this->controllerFactory()->runAction();
        }
        catch(AB_Exception $exception)
        {
            $exception->setController($controller);
            $exception->setAction($action);

            $has_error = true;
            $error = ((string) $exception);

            /* notices and warnings are ok, otherwise, set error response */

            if($exception->getCode() == E_USER_ERROR)
            {
                $this->response->setStatus(AB_Response::STATUS_ERROR);
            }

            /* log AB_Exception */

            AB_Log::writeException($exception);
        }
        catch(Exception $exception)
        {
            $has_error = true;
            $error = "Exception: " . $exception->getMessage() . "; " . 
                     "status: " . $exception->getCode() . "; " . 
                     "file: " . $exception->getFile() . "; " . 
                     "line: " . $exception->getLine() . "; " . 
                     "trace: " . $exception->getTraceAsString();

            /* unexpected exceptions are serious errors */

            $this->response->setStatus(AB_Response::STATUS_ERROR);
 
            /* log Exception */

            AB_Log::write($error, E_USER_ERROR, $controller, $action);
        }

        /* show errors */

        if($has_error)
        {
            /* show error message in browser */

            if(error_reporting() > 0)
            {
                $this->response->setBody("<pre>" . $error . "</pre>");
            }

            /* run error controller actions */

            else
            {
                $this->controllerFactory('Error')->runAction('status' . 
                    $this->response->getStatus());
            }
        }

        /* send response */

        $this->response->send();
    }

    /**
     * Initialize controller class
     *
     * @param   string          $name   Controller name
     * @throws  AB_Exception
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
            $reflection = new ReflectionClass($class_name);

            if($reflection->isAbstract() == false)
            {
                $controller = new $class_name($this->request, $this->response);
            }
        }

        if(is_object($controller) == false)
        {
            $this->response->setStatus(AB_Response::STATUS_NOT_FOUND);
            throw new AB_Exception(
                "controller (" . $name . ") not found",
                E_USER_WARNING);
        }

        return $controller;
    }
}
