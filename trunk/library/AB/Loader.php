<?php

/**
 * Class loader
 *
 * @category    Autoblog
 * @package     AB
 */
class AB_Loader
{
    /**
     * Class loader
     *
     * @param   string  $name   Class name
     * @return  void
     */
    public static function autoload ($name)
    {
        if(class_exists($name) == false)
        {
            if(strpos($name, "AB_") === 0)          self::loadAB($name);
            elseif(strpos($name, "Zend_") === 0)    self::loadZend($name);
            elseif(strpos($name, "Controller") > 0) self::loadController($name);
            elseif(strpos($name, "Helper") > 0)     self::loadHelper($name);
            else                                    self::loadModel($name);
        }
    }

    /**
     * Autoload register
     *
     * @return  void
     */
    public static function register ()
    {
        spl_autoload_register (array('AB_Loader', 'autoload'));
    }

    /**
     * Controller loader
     *
     * @param   string  $name   Class name
     * @return  void
     */
    public static function loadController($name)
    {
        $path = APPLICATION_PATH . "/controller/" . $name . ".php";

        if(file_exists($path))
        {
            include $path;
        }
    }

    /**
     * Helper loader
     *
     * @param   string  $name   Class name
     * @return  void
     */
    public static function loadHelper($name)
    {
        $path = APPLICATION_PATH . "/view/helper/" . $name . ".php";

        if(file_exists($path))
        {
            include $path;
        }
    }

    /**
     * Model loader
     *
     * @param   string  $name   Class name
     * @return  void
     */
    public static function loadModel($name)
    {
        $path = APPLICATION_PATH . "/model/" . $name . ".php";

        if(file_exists($path))
        {
            include $path;
        }
    }

    /**
     * AB loader
     *
     * @param   string  $name   Class name
     * @return  void
     */
    public static function loadAB($name)
    {
        $path = LIBRARY_PATH . "/AB/" . substr($name, 3) . ".php";

        if(file_exists($path))
        {
            include $path;
        }
    }

    /**
     * Zend loader
     *
     * @param   string  $name   Class name
     * @return  void
     */
    public static function loadZend($name)
    {
        if(class_exists("Zend_Loader") == false)
        {
            if(file_exists($zend = LIBRARY_PATH . "/Zend/Loader.php"))
            {
                include $zend;

            }
        }

        if(class_exists("Zend_Loader"))
        {
            Zend_Loader::loadClass($name);
        }
        else
        {
            throw new Exception("Zend_Loader not found");
        }
    }
}
