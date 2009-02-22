<?php

/**
 * Error controller class
 * 
 * @category    Blotomate
 * @package     Controller
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class ErrorController extends AB_Controller
{
    /**
     * Error controller constructor
     *
     * @param   AB_Request  $request
     * @param   AB_Response $response
     * @return void
     */
    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
        $this->setViewLayout(null);
    }

    /**
     * Action magic method
     *
     * @param   string  $method
     * @param   array   $arguments
     * @return  void
     */
    protected function __call($method, $arguments)
    {
        if(($position = strpos($method, 'Action')) > 0)
        {
            $template = "Error/" . substr($method, 0, $position);

            if(strlen(AB_View::getTemplatePath($template)) > 0)
            {
                $this->setViewTemplate($template);
            }
            else
            {
                $this->setViewTemplate(null);
                $this->setResponseRedirect(BASE_URL);
            }
        }
    }
}
