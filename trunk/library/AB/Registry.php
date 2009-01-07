<?php

/* AUTOBLOG REGISTRY */

class AB_Registry
{
    private static $instance;

    private $data = array();


    private function __construct() { }
    private function __clone() { }

    public static function singleton()
    {
        if(is_null(self::$instance) == true)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __get($name)
    {
        $value = null;

        if(array_key_exists($name, $this->data))
        {
            $value = $this->data[$name];
        }

        return $value;
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }
}
