<?php

/* AUTOBLOG REQUEST */

class AB_Request
{
    private $path = "/";
    private $controller = "Index";
    private $action = "index";
    private $parameters = array();


    public function __construct()
    {
        $this->path = self::getPath();

        $arguments = explode ("/", trim($this->path, "/"));
        $total_arguments = count($arguments);

        if(empty($arguments[0]) == false)
        {
            $this->controller = ucfirst($arguments[0]);
        }

        if($total_arguments > 1)
        {
            if(empty($arguments[1]) == false)
            {
                $this->action = $arguments[1];
            }
        }

        if ($total_arguments == 3)
        {
            if (empty($arguments[2]) == false)
            {
                $this->parameters += array('id' => $arguments[2]);
            }
        }

        if ($total_arguments > 3 && $total_arguments % 2 == 0)
        {
            $k = array();
            $v = array();

            for($i = 2; $i < $total_arguments; $i++)
            {
                $i % 2 == 0 ? 
                    array_push($k, $arguments[$i]) : 
                    array_push($v, $arguments[$i]);
            }

            $this->parameters = array_combine($k, $v);
        }
    }

    public function getController()
    {
        return $this->controller;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public static function getPath()
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

        return $path;
    }
}
