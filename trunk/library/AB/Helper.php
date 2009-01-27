<?php

/**
 * Helper class
 * 
 * @category    Blotomate
 * @package     AB
 */
class AB_Helper
{
    /**
     * View
     * 
     * @var AB_View
     */
    private $view;

    /**
     * View helper constructor
     *
     * @param   AB_View $view
     * @return void
     */
    public function __construct($view)
    {   
        $this->view = $view;
    }

    /**
     * HREF
     *
     * @param   string  $label
     * @param   string  $controller
     * @param   string  $action
     * @param   array   $parameters
     * @return  void
     */
    public function href($label, $controller=null, $action=null, 
                         $parameters=array())
    {
        $url = AB_Request::url($controller, $action, $parameters);

        echo "<a href=\"" . $url . "\">" . $label . "</a>";
    }

    /**
     * Javascript referencing
     *
     * @param   string  $name     Javascript file name (with .js)
     * @return  void
     */
    public function script($name)
    {
        echo "<script type=\"text/javascript\" " . 
             "src=\"" . BASE_URL . 
             "/js/" . $name . "\"></script>\n";
    }

    /**
     * Style referencing
     *
     * @param   string  $name     CSS file name (with .css)
     * @param   string  $media    CSS media
     * @return  void
     */
    public function style($name, $media="screen")
    {
        echo "<style type=\"text/css\" " . 
             "media=\"" . $media . "\">" . 
             "@import url(\"" . BASE_URL . 
             "/css/" . $name . "\");</style>\n";
    }

    /**
     * Javascript including
     *
     * @return  void
     */
    public function includeJavascript()
    {
        $template = $this->view->getRequest()->getController() . "/" . 
                    $this->view->getRequest()->getAction();

        $path = APPLICATION_PATH . "/view/template/" . $template . ".js";

        if(file_exists($path) == true)
        {
            echo "<script type=\"text/javascript\">\n";
            include $path;
            echo "</script>\n";
        }
    }

    /**
     * Style sheet including
     *
     * @return  void
     */
    public function includeStyleSheet()
    {
        $template = $this->view->getRequest()->getController() . "/" . 
                    $this->view->getRequest()->getAction();

        $path = APPLICATION_PATH . "/view/template/" . $template . ".css";

        if(file_exists($path) == true)
        {
            echo "<style type=\"text/css\">\n";
            include $path;
            echo "</style>\n";
        }
    }

    /**
     * Get helper data from action method
     *
     * @param   string  $helper_key Array key from helper array
     * @return  mixed
     */
    private function getHelperData($helper_key)
    {
        $data = array();

        if(is_array($this->view->helper))
        {
            $helper = $this->view->helper;
        
            if(array_key_exists($helper_key, $helper))
            {
                $data = $helper[$helper_key];
            }
        }

        return $data;
    }
}
