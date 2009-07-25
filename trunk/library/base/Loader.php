<?php

/**
 * Base Loader
 *
 * @category    PostCanal
 * @package     Base Library
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class B_Loader
{
    /**
     * Autoload register
     *
     * @return  void
     */
    public static function register ()
    {
        spl_autoload_register (array('B_Loader', 'autoload'));
    }

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
            if    (strpos($name, "L_") === 0)    self::fromLibrary($name);
            elseif(strpos($name, "C_") === 0)    self::fromController($name);
            elseif(strpos($name, "Zend_") === 0) self::fromZend($name);
            else                                 self::fromModel($name);
        }
    }

    /**
     * Application Library loader
     *
     * @param   string  $name   Class name
     * @return  void
     */
    public static function fromLibrary($name)
    {
        $path = APPLICATION_PATH . "/library/" . substr($name, 2) . ".php";

        if(class_exists($name) == false)
        {
            if(file_exists($path))
            {
                include $path;
            }
        }
    }

    /**
     * Controller loader
     *
     * @param   string  $name   Class name
     * @return  void
     */
    public static function fromController($name)
    {
        $path = APPLICATION_PATH . "/controller/" . substr($name, 2) . ".php";

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
    public static function fromModel($name)
    {
        $path = APPLICATION_PATH . "/model/" . $name . ".php";

        if(file_exists($path))
        {
            include $path;
        }
    }

    /**
     * Zend loader
     *
     * @param   string  $name   Class name
     * @throw   B_Exception
     * @return  void
     */
    public static function fromZend($name)
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
            $_m = 'class Zend_Loader not found';
            $_d = array('method' => __METHOD__);
            B_Log::write($_m, E_ERROR, $_d);
        }
    }
}
