<?php

/**
 * Application dispatcher
 * 
 * @category    Blotomate
 * @package     AB
 * @author      Rafael Castilho <rafael@castilho.biz>
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
            $has_error = true;

            $exception->controller = $controller;
            $exception->action = $action;

            $error = ((string) $exception);

            /* notices and warnings are ok, otherwise, set error response */

            if($exception->getCode() == E_USER_ERROR)
            {
                $this->response->setStatus(AB_Response::STATUS_ERROR);
            }

            /* log AB_Exception */

            $exception->log();
        }
        catch(Exception $exception)
        {
            $has_error = true;

            $error = "message: " . $exception->getMessage() . "; " .
                     "code: " . $exception->getCode() . "; " .
                     "file: " . $exception->getFile() . "; " .
                     "line: " . $exception->getLine() . "; " .
                     "trace: " . $exception->getTraceAsString();

            /* unexpected exceptions are serious errors */

            $this->response->setStatus(AB_Response::STATUS_ERROR);
 
            /* log Exception */

            $attributes = array
            (
                'method' => __METHOD__, 'controller' => $controller, 'action' => $action
            );

            AB_Log::write($error, E_USER_ERROR, $attributes);
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
            $message = "controller (" . $name . ") not found";
            $data = array('method' => __METHOD__);
            throw new AB_Exception($message, E_USER_WARNING);
        }

        return $controller;
    }
}
