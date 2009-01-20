<?php

/**
 * Helper class
 * 
 * @category    Autoblog
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
            echo "<script>\n";
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
            echo "<style>\n";
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
