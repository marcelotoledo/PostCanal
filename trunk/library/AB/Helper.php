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
     * URL (without initial /)
     *
     * @param   string  $controller
     * @param   string  $action
     * @param   array   $parameters
     * @return  string
     */
    public static function url($controller=null, $action=null, $parameters=array())
    {
        echo self::relativeURL(AB_Request::url($controller, $action, $parameters));
    }

    /**
     * HREF (with initial /)
     *
     * @param   string  $controller
     * @param   string  $action
     * @param   array   $parameters
     * @return  string
     */
    public static function href($controller=null, $action=null, $parameters=array())
    {
        $url = self::relativeURL(AB_Request::url($controller, $action, $parameters));

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
    public static function a($label, $controller=null, $action=null, $parameters=array())
    {
        echo "<a href=\"";
        self::href($controller, $action, $parameters);
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
    public static function script_src($name)
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
    public static function script($name, $type="text/javascript")
    {
        echo "<script type=\"" . $type . "\" src=\"";
        self::script_src($name);
        echo "\"></script>\n";
    }

    /**
     * Style URL
     *
     * @param   string  $name
     * @return  void
     */
    public static function style_url($name)
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
    public static function style($name, $type="text/css", $media="screen")
    {
        echo "<style type=\"" . $type . "\" media=\"" . $media . "\">";
        echo "@import url(\"";
        self::style_url($name);
        echo "\");</style>\n";
    }

    /**
     * Image source
     *
     * @param   string  $path
     * @return  void
     */
    public static function img_src($path)
    {
        echo self::relativeURL(BASE_URL) . "/image/" . $path;
    }

    /**
     * Image
     *
     * @param   string  $path   Image path
     * @return  void
     */
    public static function img($path, $alt="")
    {
        echo "<img src=\"";
        echo self::img_src($path);
        echo "\" alt=\"" . $alt . "\">\n";
    }

    /**
     * Javascript including
     *
     * @return  void
     */
    public static function include_javascript()
    {
        $registry = AB_Registry::singleton();
        $template = $registry->view->template;
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
    public static function include_stylesheet()
    {
        $registry = AB_Registry::singleton();
        $template = $registry->view->template;
        $path = APPLICATION_PATH . "/view/template/" . $template . ".css";

        if(file_exists($path) == true)
        {
            echo "<style type=\"text/css\">\n";
            include $path;
            echo "</style>\n";
        }
    }

    /**
     * Session
     *
     * @param   string  $name
     * @return  string  
     */
    public static function session($name)
    {
        $registry = AB_Registry::singleton();
        $session = $registry->session->object;
        echo (isset($session) && is_object($session)) ? $session->{$name} : '';
    }

    /**
     * Translation
     *
     * @param   string  $name
     * @return  string  
     */
    public static function translation($name)
    {
        $registry = AB_Registry::singleton();
        $translation = $registry->translation->object;
        
        $value = null;
        if(isset($translation) && is_object($translation)) $value = $translation->{$name};
        if(strlen($value) == 0) $value = $name;

        echo $value;
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
