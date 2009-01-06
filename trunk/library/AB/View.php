<?php

/* AUTOBLOG VIEW CLASS */

class AB_View
{
    private $request;

    private $data = array();


    public function __get($name)
    {
        $value = null;

        if(array_key_exists($name, $this->data))
        {
            $value = $this->data[$name];
        }

        return $value;
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function __construct($request, $data)
    {
        $this->request = $request;
        $this->data = $data;
    }

    public function render()
    {
        $file_path = APPLICATION_PATH . "/layout/layout.php";

        if(file_exists($file_path) == true)
        {
            include $file_path;
        }
        else
        {
            $this->renderAction();
        }
    }

    private function renderAction()
    {
        $file_name = $this->request->getController() . "/";
        $file_name.= $this->request->getAction() . ".php";
        $file_path = APPLICATION_PATH . "/view/" . $file_name;

        if(file_exists($file_path) == true)
        {
            include $file_path;
        }
        else
        {
            throw new Exception("file " . $file_name . " not found");
        }
    }
}
