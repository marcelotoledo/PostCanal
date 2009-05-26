<?php

/**
 * Base Bootstrap
 *
 * @category    PostCanal
 * @package     Base Library
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class B_Bootstrap
{
    /**
     * Default controller name
     *
     * @var string
     */
    public $default_controller = "index";

    /**
     * Default action name
     *
     * @var string
     */
    public $default_action = "index";

    /**
     * Translation loading list
     *
     * @var array
     */
    public $translation_load = array();


    /**
     * Run bootstrap
     */
    public function run()
    {
        $has_error = false;
        $message = ((string) null); 

        $registry = B_Registry::singleton();
        $view = null;

        $registry->request()->object = null;
        $registry->response()->object = null;
        $registry->session()->object = null;
        $registry->translation()->object = null;

        /* initialize request and response */

        $request = new B_Request();
        $response = new B_Response();
        $registry->request()->object = $request;
        $registry->response()->object = $response;

        /* check controller */

        $controller_name = $request->getController();

        if(strlen($controller_name) == 0)
        {
            $controller_name = $this->default_controller;
        }

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

            if(strlen($action_name) == 0)
            {
                $action_name = $this->default_action;
            }

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
                $template = ucfirst($controller_name) . "/" . $action_name;
                $view->setTemplate($template);
                $controller->view = $view;

                /* initialize session */

                $session_name = $registry->session()->name;
                $session = new B_Session($session_name);
                $registry->session()->object = $session;

                /* initialize translation */

                $culture = $registry->translation()->culture;
                $translation = new B_Translation($culture);
                $registry->translation()->object = $translation;

                /* translation load */

                $this->translation_load[] = 'application';
                $this->translation_load[] = $controller_name;
                $this->translation_load[] = $controller_name . "/" . $action_name;
                $translation->load($this->translation_load);

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
     * Controller factory
     *
     * @param   string          $name   Controller name
     *
     * @return  B_Controller
     */
    protected static function factory($name)
    {
        $controller = null;
        $class_name = 'C_' . ucfirst($name);

        if(class_exists($class_name))
        {
            $controller = new $class_name();
        }

        return $controller;
    }

    /**
     * Error response body
     *
     * @param   integer $status
     */
    protected static function error($status)
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
}
