<?php

/**
 * Main application controller
 * 
 * @category    Blotomate
 * @package     AB
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
class AB_Main
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

        $registry = AB_Registry::singleton();

        /* initialize request and response */

        $request = new AB_Request();
        $response = new AB_Response();
        $registry->request->object = $request;
        $registry->response->object = $response;

        /* check controller */

        $controller_name = $request->getController();

        if(($controller = self::controllerFactory($controller_name)) == null)
        {
            $has_error = true;
            $message = "controller (" . $controller_name . ") not found";
            $response->setStatus(AB_Response::STATUS_NOT_FOUND);
        }
        else
        {
            /* assign registry to controller */

            $controller->registry = $registry;

            /* check action */

            $action_name = $request->getAction();

            if($controller->checkAction($action_name) == false)
            {
                $has_error = true;
                $message = "action (" . $action_name . ") not found";
                $response->setStatus(AB_Response::STATUS_NOT_FOUND);
            }
            else
            {
                /* initialize view */

                $view = new AB_View();
                $view->registry = $registry;
                $layout = strtolower($controller_name);
                $view->setLayout($layout);
                $template = $controller_name . "/" . $action_name;
                $view->setTemplate($template);
                $controller->view = $view;

                /* initialize session */

                $session_name = $registry->session->name;
                $session = new AB_Session($session_name);
                $controller->session = $session;
                $registry->session->object = $session;

                /* initialize translation */

                $culture = $registry->translation->culture;
                $translation = new AB_Translation($culture);
                $controller->translation = $translation;
                $registry->translation->object = $translation;

                /* run action */

                try
                {
                    $controller->runAction($action_name);
                }
                catch(AB_Exception $exception)
                {
                    /* set error */

                    $exception->controller = $controller_name;
                    $exception->action = $action_name;
                    $has_error = true;
                    $message = ((string) $exception);

                    /* set response status */

                    if($exception->getCode() == E_USER_ERROR)
                    {
                        $response->setStatus(AB_Response::STATUS_ERROR);
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

                    $response->setStatus(AB_Response::STATUS_ERROR);
         
                    /* log exception */

                    $_d = array ('method' => __METHOD__, 
                                 'controller' => $controller_name, 
                                 'action' => $action_name);
                    AB_Log::write($message, E_USER_ERROR, $_d);
                }
            }
        }

        /* error reporting */

        if($has_error)
        {
            if($response->isXML() == false)
            {
                $controller_name = 'Error';
                $status = $response->getStatus();
                $action_name = 'status' . $status;

                /* run error controller actions */

                try
                {
                    $controller = self::controllerFactory($controller_name);
                    $controller->runAction($action_name);
                }
                catch(Exception $exception)
                {
                    $response->setBody("<h2>error " . $status . "</h2>");
                }
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
     * Initialize controller class
     *
     * @param   string          $name   Controller name
     * @return  AB_Controller
     */
    private static function controllerFactory($name)
    {
        $controller = null;
        $class_name = $name . "Controller";

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
