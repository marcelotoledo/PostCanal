<?php

/**
 * Main application controller
 * 
 * @category    Blotomate
 * @package     Base
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
class B_Main
{
    /**
     * Run application
     *
     */
    public static function run()
    {
        $main = new self();
        $main->dispatch();
    }

    /**
     * Dispatch application
     *
     * @return void
     */
    public function dispatch()
    {
        $has_error = false;
        $message = ((string) null); 

        $registry = B_Registry::singleton();
        $view = null;

        /* initialize request and response */

        $request = new B_Request();
        $response = new B_Response();
        $registry->request->object = $request;
        $registry->response->object = $response;

        /* check controller */

        $controller_name = $request->getController();

        if(($controller = self::factory($controller_name)) == null)
        {
            $has_error = true;
            $message = "controller (" . $controller_name . ") not found";
            $response->setStatus(B_Response::STATUS_NOT_FOUND);
        }
        else
        {
            /* assign registry to controller */

            $controller->registry = $registry;

            /* check action */

            $action_name = $request->getAction();

            if($controller->check($action_name) == false)
            {
                $has_error = true;
                $message = "action (" . $action_name . ") not found";
                $response->setStatus(B_Response::STATUS_NOT_FOUND);
            }
            else
            {
                /* initialize view */

                $view = new B_View();
                $view->registry = $registry;
                $layout = strtolower($controller_name);
                $view->setLayout($layout);
                $template = $controller_name . "/" . $action_name;
                $view->setTemplate($template);
                $controller->view = $view;

                /* initialize session */

                $session_name = $registry->session->name;
                $session = new B_Session($session_name);
                $controller->session = $session;
                $registry->session->object = $session;

                /* initialize translation */

                $culture = $registry->translation->culture;
                $translation = new B_Translation($culture);
                $controller->translation = $translation;
                $registry->translation->object = $translation;

                /* run action */

                try
                {
                    $controller->before();
                    $controller->run($action_name);
                    $controller->after();
                }
                catch(B_Exception $exception)
                {
                    /* set error */

                    $exception->controller = $controller_name;
                    $exception->action = $action_name;
                    $has_error = true;
                    $message = ((string) $exception);

                    /* set response status */

                    if($exception->getCode() == E_USER_ERROR)
                    {
                        $response->setStatus(B_Response::STATUS_ERROR);
                    }

                    /* log exception */

                    $exception->writeLog();
                }
                catch(Exception $exception)
                {
                    /* set error */

                    $has_error = true;

                    $message = "message: " . $exception->getMessage() . "; " .
                               "code: "    . $exception->getCode() . "; " .
                               "file: "    . $exception->getFile() . "; " .
                               "line: "    . $exception->getLine() . "; " .
                               "trace: "   . $exception->getTraceAsString();

                    /* unexpected exceptions are fatal errors */

                    $response->setStatus(B_Response::STATUS_ERROR);
         
                    /* log exception */

                    $_d = array ('method' => __METHOD__, 
                                 'controller' => $controller_name, 
                                 'action' => $action_name);
                    B_Log::write($message, E_USER_ERROR, $_d);
                }
            }
        }

        /* error reporting */

        if($has_error)
        {
            if($response->isXML() == false)
            {
                $status = $response->getStatus();
                $response->setBody(self::error($status));
            }

            /* show error message in browser */

            if(error_reporting() > 0)
            {
                $response->setBody($message);
            }

        }

        /* send response */

        $response->send();
    }

    /**
     * Error response body
     *
     * @param   integer $status
     */
    private static function error($status)
    {
        $path = BASE_PATH . "/public/" . $status . ".html";
        $s = "<h1>error " . $status . "</h2>";

        if(file_exists($path))
        {
            $f = fopen($path, "r");
            $s = fread($f, filesize($path));
            fclose($f);
        }

        return $s;
    }


    /**
     * Initialize controller class
     *
     * @param   string          $name   Controller name
     * @return  B_Controller
     */
    private static function factory($name)
    {
        $controller = null;
        $class_name = 'C_' . $name;

        if(class_exists($class_name))
        {
            $reflection = new ReflectionClass($class_name);

            if($reflection->isAbstract() == false)
            {
                $controller = new $class_name();
            }
        }

        return $controller;
    }
}
