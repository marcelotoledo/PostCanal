<?php

/* AUTOBLOG DISPATCHER */

class AB_Dispatcher
{
    private static $instance;


    private function __construct() { }
    private function __clone() { }

    public static function singleton()
    {
        if(is_null(self::$instance))
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function dispatch()
    {
        $request = new AB_Request();
        $response = new AB_Response();

        $controller = null;

        try
        {
            $controller = self::controllerFactory($request, $response);
        }
        catch(Exception $exception)
        {
            $response->setBody($exception);
        }

        if(is_object($controller))
        {
            try
            {
                $controller->render();
            }
            catch(Exception $exception)
            {
                $response->setBody($exception);
            }
        }
       
        $response->send();
    }

    public static function controllerFactory($request, $response)
    {
        $controller_name = $request->getController() . "Controller";
        $controller_path = APPLICATION_PATH . "/controller/" . $controller_name . ".php";

        if(file_exists($controller_path) == true)
        {
            include_once $controller_path;
            return new $controller_name($request, $response);
        }
        else
        {
            throw new Exception ("controller " . $request->getController() . " not found");
        }
    }
}
