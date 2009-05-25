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
            if    (strpos($name, "A_") === 0)    self::application($name);
            elseif(strpos($name, "C_") === 0)    self::controller($name);
            elseif(strpos($name, "H_") === 0)    self::helper($name);
            elseif(strpos($name, "Zend_") === 0) self::zend($name);
            else                                 self::model($name);
        }
    }

    /**
     * Application library loader
     *
     * @param   string  $name   Class name
     * @return  void
     */
    public static function application($name)
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
    public static function controller($name)
    {
        $path = APPLICATION_PATH . "/controller/" . substr($name, 2) . ".php";

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
    public static function helper($name)
    {
        $path = APPLICATION_PATH . "/view/helper/" . substr($name, 2) . ".php";

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
    public static function model($name)
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
    public static function zend($name)
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
            $_m = "class (Zend_Loader) not found";
            $_d = array('method' => __METHOD__);
            throw new B_Exception($_m, E_USER_ERROR, $_d);
        }
    }
}
