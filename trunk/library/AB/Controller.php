<?php

/* AUTOBLOG CONTROLLER */

abstract class AB_Controller
{
    private $request;
    private $response;


    public function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function render()
    {
        $action_name = $this->request->action . "Action";

        if(is_callable(array($this, $action_name)) == true)
        {
            $view = new AB_View($this->request, $this->{$action_name}());

            ob_start();
            $view->render();
            $this->response->body = ob_get_clean();
        }
        else
        {
            throw new Exception("action " . $this->request->action . " not found");
        }
    }
}
