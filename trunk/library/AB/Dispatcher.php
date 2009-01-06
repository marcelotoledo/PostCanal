<?php

/* AUTOBLOG DISPATCHER */

class AB_Dispatcher
{
    public static function dispatch()
    {
        $request = new AB_Request();
        $response = new AB_Response();

        $controller = null;
        $controller_name = $request->controller . "Controller";
        $controller_path = APPLICATION_PATH . "/controller/" . $controller_name . ".php";

        if(file_exists($controller_path) == true)
        {
            include_once $controller_path;
            $controller = new $controller_name($request, $response);
        }
        else
        {
            $response->body = "controller " . $request->controller . " not found";
        }

        try
        {
            if(is_object($controller))
            {
                $controller->render();
            }
        }
        catch(Exception $exception)
        {
            $response->body = $exception;
        }
        
        $response->send();
    }
}
