<?php

/**
 * Helper class
 * 
 * @category    Blotomate
 * @package     Base
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
class B_Helper
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
        echo self::relative(B_Request::url($controller, $action, $parameters));
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
        $url = self::relative(B_Request::url($controller, $action, $parameters));
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
        echo "\">" . $label . "</a>";
    }

    /**
     * Script source
     *
     * @param   string  $name
     * @return  void
     */
    public static function script_src($name)
    {
        echo self::relative(BASE_URL) . "/script/" . $name;
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
        echo self::relative(BASE_URL) . "/style/" . $name;
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
        /*
        echo "<style type=\"" . $type . "\" media=\"" . $media . "\">";
        echo "@import url(\"";
        self::style_url($name);
        echo "\");</style>\n";
        */
        echo "<link rel=\"stylesheet\" href=\"";
        self::style_url($name);
        echo "\" type=\"" . $type . "\" media=\"" . $media . "\">\n";
    }

    /**
     * Image source
     *
     * @param   string  $path
     * @return  void
     */
    public static function img_src($path)
    {
        echo self::relative(BASE_URL) . "/image/" . $path;
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
        self::img_src($path);
        echo "\" alt=\"" . $alt . "\">\n";
    }

    /**
     * Get relative URL
     *
     * @param   string  $url
     * @return  string
     */
    public static function relative($url)
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
