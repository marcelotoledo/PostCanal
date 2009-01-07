<?php

/* AUTOBLOG LOADER */

class AB_Loader
{
    public static function autoload ($class_name)
    {
        $file_path = null;

        if(strpos($class_name, "AB_") === 0)
        {
            $file_name = substr($class_name, 3) . ".php";
            $file_path = LIBRARY_PATH . "/AB/" . $file_name; 
        }
        else
        {
            $file_name = $class_name . ".php";
            $file_path = APPLICATION_PATH . "/model/" . $file_name;
        }

        include_once $file_path;
    }

    public static function register ()
    {
        spl_autoload_register (array('AB_Loader', 'autoload'));
    }
}
