<?php

/**
 * Helper class
 * 
 * @category    Blotomate
 * @package     AB
 * @author      Rafael Castilho <rafael@castilho.biz>
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
     * URL (without initial /)
     *
     * @param   string  $controller
     * @param   string  $action
     * @param   array   $parameters
     * @return  string
     */
    public function url($controller=null, $action=null, $parameters=array())
    {
        echo self::relativeURL(AB_Request::url($controller, 
                                               $action, 
                                               $parameters));
    }

    /**
     * HREF (with initial /)
     *
     * @param   string  $controller
     * @param   string  $action
     * @param   array   $parameters
     * @return  string
     */
    public function href($controller=null, $action=null, $parameters=array())
    {
        $url = self::relativeURL(AB_Request::url($controller, 
                                                 $action, 
                                                 $parameters));

        echo strlen($url) > 0 ? $url : "/";
    }

    /**
     * Anchor
     *
     * @param   string  $label
     * @param   string  $controller
     * @param   string  $action
     * @param   array   $parameters
     * @return  void
     */
    public function a($label, $controller=null, $action=null, 
                      $parameters=array())
    {
        echo "<a href=\"";
        $this->href($controller, $action, $parameters);
        echo "\">";
        echo $label;
        echo "</a>";
    }

    /**
     * Script source
     *
     * @param   string  $name
     * @return  void
     */
    public function script_src($name)
    {
        echo self::relativeURL(BASE_URL) . "/script/" . $name;
    }

    /**
     * Script
     *
     * @param   string  $name     Script file name (with .js)
     * @param   string  $type     Script type
     * @return  void
     */
    public function script($name, $type="text/javascript")
    {
        echo "<script type=\"" . $type . "\" src=\"";
        $this->script_src($name);
        echo "\"></script>\n";
    }

    /**
     * Style URL
     *
     * @param   string  $name
     * @return  void
     */
    public function style_url($name)
    {
        echo self::relativeURL(BASE_URL) . "/style/" . $name;
    }

    /**
     * Style
     *
     * @param   string  $name     CSS file name (with .css)
     * @param   string  $media    CSS media
     * @return  void
     */
    public function style($name, $type="text/css", $media="screen")
    {
        echo "<style type=\"" . $type . "\" media=\"" . $media . "\">";
        echo "@import url(\"";
        $this->style_url($name);
        echo "\");</style>\n";
    }

    /**
     * Image source
     *
     * @param   string  $path
     * @return  void
     */
    public function img_src($path)
    {
        echo self::relativeURL(BASE_URL) . "/image/" . $path;
    }

    /**
     * Image
     *
     * @param   string  $path   Image path
     * @return  void
     */
    public function img($path, $alt="")
    {
        echo "<img src=\"";
        echo $this->img_src($path);
        echo "\" alt=\"" . $alt . "\">\n";
    }



    /**
     * Javascript including
     *
     * @return  void
     */
    public function include_javascript()
    {
        $template = $this->view->getTemplate();
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
    public function include_stylesheet()
    {
        $template = $this->view->getTemplate();
        $path = APPLICATION_PATH . "/view/template/" . $template . ".css";

        if(file_exists($path) == true)
        {
            echo "<style type=\"text/css\">\n";
            include $path;
            echo "</style>\n";
        }
    }

    /**
     * Get relative URL
     *
     * @param   string  $url
     * @return  string
     */
    protected static function relativeURL($url)
    {
        $relative = $url;

        if(($position = strpos($relative, "//")) > 0)
        {
            $relative = substr($relative, $position + 2);
        }

        if(($position = strpos($relative, "/")) > 0)
        {
            $relative = substr($relative, $position);
        }
        else
        {
            $relative = "";
        }

        return $relative;
    }
}
