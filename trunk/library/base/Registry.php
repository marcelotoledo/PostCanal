<?php

/**
 * Base Registry
 *
 * Generic storage class to manage global data
 *
 * @category    PostCanal
 * @package     Base Library
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class B_Registry
{
    /**
     * Singleton instance
     * 
     * @var B_Registry
     */
    private static $instance;


    private function __construct() { }
    private function __clone() { }

    /**
     * Singleton constructor
     */
    protected static function singleton()
    {
        if(is_null(self::$instance) == true)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Set overloading
     */
    private function __set ($k, $v)
    {
        $this->{$k} = $v;
    }

    /**
     * Get overloading
     */
    public function __get ($k)
    {
        return isset($this->{$k}) ? $this->{$k} : null;
    }

    /**
     * Loader
     *
     * @param   string  $filename
     * @param   string  $type
     */
    public static function load($filename=null, $type='xml')
    {
        if(strlen($filename) > 0 && file_exists($filename))
        {
            switch (strtolower($type))
            {
                case 'xml' : 
                    $xml = simplexml_load_file($filename); 

                    if(is_object($xml)) 
                        if(count($xml) > 0) 
                            self::fromXML($xml->children(), self::singleton());
                break;
            }
        }
    }

    /**
     * Static setter
     *
     * @param   string  $path
     * @param   mixed   $value
     */
    public static function set($path, $value)
    {
        $r = self::singleton();
        $a = explode('/', $path);
        $j = array_pop($a);

        foreach($a as $i)
        {
            if(strlen($i)==0) throw new B_Exception('invalid path', E_WARNING);
            if(!isset($r->{$i})) $r->{$i} = new self();
            $r = $r->{$i};
        }

        $r->{$j} = $value;
    }

    /**
     * Static getter
     *
     * @param   string  $path
     * @return  mixed
     */
    public static function get($path)
    {
        $r = self::singleton();
        $a = explode('/', $path);

        foreach($a as $i)
        {
            if(strlen($i)==0) throw new B_Exception('invalid path', E_WARNING);
            $r = $r->{$i};
        }

        return $r;
    }

    /**
     * Load data from XML
     *
     * @param   SimpleXMLElement    $xml
     * @param   object              $obj
     * @return  void
     */
    protected static function fromXML($xml, $obj)
    {
        foreach($xml as $k => $v)
        {
            if(count($v) > 0) 
            {
                $obj->{$k} = new self();
                self::fromXML($v, $obj->{$k});
            }
            else
            {
                $obj->{$k} = ((string) $v);
            }
        }
    }
}
