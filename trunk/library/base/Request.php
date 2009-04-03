<?php

/**
 * Request
 * 
 * @category    Blotomate
 * @package     Base
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
class B_Request
{
    /**
     * Method constants
     */
    const METHOD_GET  = "GET";
    const METHOD_POST = "POST";


    /**
     * Request path
     *
     * @var string
     */
    private $path = "/";

    /**
     * Request method
     *
     * @var string
     */
    private $method;

    /**
     * Controller name
     *
     * @var string
     */
    private $arguments = array();


    /**
     * Request constructor
     *
     * @return  void
     */
    public function __construct()
    {
        $this->initialize();
    }

    /**
     * Get overloading
     *
     * @param   string  $name
     * @return  string
     */
    public function __get($name)
    {
        $value = null;

        if(array_key_exists($name, $_REQUEST))
        {
            $value = $_REQUEST[$name];
        }

        return $value;
    }

    public function __set($name, $value) { } /* request is read-only */
    
    /**
     * Request initializer
     *
     * @return void
     */
    private function initialize()
    {
        /* initialize */

        $this->path = self::pathFromServer();
        $this->method = self::methodFromServer();

        $arguments = explode("/", trim($this->path, "/"));
        $total = count($arguments);

        /* filter arguments */

        for($i=0;$i<$total;$i++)
        {
            $arguments[$i] = strtolower($arguments[$i]);
            $arguments[$i] = preg_replace("/[^a-z0-9]/", "", $arguments[$i]);
        }

        $this->arguments = $arguments;
    }

    /**
     * Request path
     *
     * @return  string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Request method
     *
     * @return  string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get request argument
     *
     * @parameter   integer     $i
     * @return      array
     */
    public function getArgument($i)
    {
        return $this->arguments[$i];
    }

    /**
     * Request controller
     *
     * @return  array
     */
    public function getController()
    {
        return array_key_exists(0, $this->arguments) ? $this->arguments[0] : null;
    }

    /**
     * Request action
     *
     * @return  array
     */
    public function getAction()
    {
        return array_key_exists(1, $this->arguments) ? $this->arguments[1] : null;
    }

    /**
     * URL for controller / action
     *
     * @param   string  $controller
     * @param   string  $action
     * @param   array   $parameters
     * @param   array   $base           Base URL (when <> BASE_URL)
     * @return  string
     */
    public static function url ($controller=null, $action=null, 
                                $parameters=array(), $base=null)
    {
        $url = $base ? $base : BASE_URL;

        if(strlen($controller) > 0)
        {
            $url .= "/" . $controller;

            if(strlen($action) > 0)
            {
                $url .= "/" . $action;
            }
        }

        if(count($parameters) > 0)
        {
            $url .= "?";
            $url_parameters = array();

            foreach($parameters as $name => $value)
            {
                $url_parameters[] = $name . "=" . urlencode($value);
            }

            $url .= implode("&", $url_parameters);
        }

        return $url;
    }

    /**
     * Path from server (tested only with Apache web server)
     *
     * @throws  B_Exception
     * @return  string
     */
    public static function pathFromServer()
    {
        $request_uri = ((string)$_SERVER['REQUEST_URI']);

        if(strlen($request_uri) == 0)
        {
            $_m = "request uri is empty";
            $_d = array('method' => __METHOD__);
            throw new B_Exception($_m, E_USER_ERROR, $_d);
        }

        $path = $request_uri;

        $script_name = ((string)$_SERVER['SCRIPT_NAME']);
 
        if(strlen($script_name) > 0)
        {
            if(strpos($path, $script_name) === 0)
            {
                $path = str_replace($script_name, "", $path);
            }
        }

        $script_dir = str_replace("/index.php", "", $script_name);

        if(strlen($script_dir) > 0)
        {
            if(strpos($path, $script_dir) === 0)
            {
                $path = str_replace($script_dir, "", $path);
            }
        }

        $query_string = ((string)$_SERVER['QUERY_STRING']);

        if(strlen($query_string) > 0)
        {
            if(strpos($path, $query_string) > 0)
            {
                $path = str_replace("?" . $query_string, "", $path);
            }
        }

        return $path;
    }

    /**
     * Method from server (tested only with Apache web server)
     *
     * @throws  B_Exception
     * @return  string
     */
    public static function methodFromServer()
    {
        $request_method = ((string)$_SERVER['REQUEST_METHOD']);

        if(strlen($request_method) == 0)
        {
            $_m  = "request method is empty";
            $_d = array('method' => __METHOD__);
            throw new B_Exception($_m, E_USER_ERROR, $_d);
        }

        return $request_method;
    }
}
