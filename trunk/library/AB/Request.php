<?php

/* AUTOBLOG REQUEST */

class AB_Request
{
    public $controller = "Index";
    public $action = "index";
    public $parameters = array();


    public function __construct()
    {
        $request = isset($_GET['ab_request']) ? $_GET['ab_request'] : "";
        $request = trim ($request, "/");

        $arguments = explode ("/", $request);
        $total_arguments = count($arguments);


        /* controller and action */

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


        /* parameters */

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
}
