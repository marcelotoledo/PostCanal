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
        $this->setViewTemplate(null);
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
            $template = substr($method, 0, $position) . ".html";
            $body = self::readTemplate($template);

            empty($body) ? 
                $this->setResponseRedirect(BASE_URL) : 
                $this->setViewData($body);
        }
    }

    /**
     * Read error template
     *
     * @param   string  $template
     * @return void
     */
    private static function readTemplate($template)
    {
        $path = APPLICATION_PATH . "/view/template/Error/" . $template;
        $body = "";

        if(file_exists($path))
        {
            $f = fopen($path, "r");
            while(!feof($f)) $body.= fgets($f);
            fclose($f);
        }

        return $body;
    }
}
