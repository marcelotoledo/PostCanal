<?php

/**
 * Request
 * 
 * @category    Autoblog
 * @package     AB
 */
class AB_Request
{
    /**
     * Request path
     *
     * @var string
     */
    private $path = "/";

    /**
     * Controller name
     *
     * @var string
     */
    private $controller = "Index";

    /**
     * Action name
     *
     * @var string
     */
    private $action = "index";


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
    
    /**
     * Request initializer
     *
     * @return void
     */
    private function initialize()
    {
        /* initialize path */

        $this->path = self::pathFromServer();

        /* initialize controller */

        $arguments = explode ("/", trim($this->path, "/"));
        $total_arguments = count($arguments);

        if(empty($arguments[0]) == false)
        {
            $this->controller = ucfirst($arguments[0]);
        }

        /* initialize action */

        if($total_arguments > 1)
        {
            if(empty($arguments[1]) == false)
            {
                $this->action = $arguments[1];
            }
        }
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
     * Controller name
     *
     * @return  string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Action name
     *
     * @return  string
     */
    public function getAction()
    {
        return $this->action;
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

        if(!empty($controller))
        {
            $url .= "/" . $controller;

            if(!empty($action))
            {
                $url .= "/" . $action;
            }
        }

        if(count($parameters) > 0)
        {
            $url .= "?";

            foreach($parameters as $name => $value)
            {
                $url .= $name . "=" . urlencode($value);
            }
        }

        return $url;
    }

    /**
     * Path from server (tested only with Apache web server)
     *
     * @return  string
     */
    public static function pathFromServer()
    {
        $request_uri = $_SERVER['REQUEST_URI'];

        if(empty($request_uri) == true)
        {
            throw new Exception("request uri is empty");
        }

        $path = $request_uri;

        $script_name = $_SERVER['SCRIPT_NAME'];
        $query_string = $_SERVER['QUERY_STRING'];
 
        if(strstr($path, $script_name))
        {
            $path = str_replace($script_name, "", $path);
        }

        if(strstr($path, "?" . $query_string))
        {
            $path = str_replace("?" . $query_string, "", $path);
        }

        /* remove relative url */

        $r = substr(BASE_URL, strpos(BASE_URL, "//") + 2);
        $r = (($i = strpos($r , "/")) > 0) ? substr($r, $i) : "";
        $path = (($i = strlen($r)) > 0) ? substr($path, $i) : $path;

        return $path;
    }
}
