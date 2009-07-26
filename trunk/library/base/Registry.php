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

    /**
     * Data
     * 
     * @var array
     */
    private $data = array();

    /**
     * Constructor 
     *
     * @param   string  $filename
     * @param   string  $type
     * @return  void
     */
    private function __construct($filename=null, $type='xml')
    {
        if(strlen($filename) > 0 && file_exists($filename))
        {
            switch (strtolower($type))
            {
                case 'xml' : 
                    $xml = simplexml_load_file($filename); 

                    if(is_object($xml)) 
                        if(count($xml) > 0) 
                            self::fromXML($xml->children(), $this->data);
                break;
            }
        }
    }

    private function __clone() { }

    /**
     * Singleton constructor
     * 
     * @return B_Dispatcher
     */
    public static function singleton($filename=null, $type='xml')
    {
        if(is_null(self::$instance) == true)
        {
            self::$instance = new self($filename, $type);
        }

        return self::$instance;
    }

    /**
     * Set overloading
     *
     * @param   string  $name
     * @param   mixed   $value
     * @return  void
     */
    public function __set ($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * Set static
     *
     * @param   mixed   $name/$hash
     * @param   mixed   $value
     * @return  void
     */
    public static function set ($arg, $value=null)
    {
        $registry = self::singleton();

        if(is_array($arg))
        {
            foreach($arg as $k => $v)
            {
                $registry->__set($k, $v);
            }
        }
        else
        {
            $registry->__set($arg, $value);
        }
    }

    /**
     * Get overloading
     * 
     * @param   string  $name
     * @return  mixed
     */
    public function __get ($name)
    {
        $value = null;

        if(array_key_exists($name, $this->data))
        {
            $value = $this->data[$name];
        }

        return $value;
    }

    /**
     * Get static
     *
     * @param   mixed   $name/$names
     * @return  mixed
     */
    public static function get ($arg)
    {
        $result = null;
        $registry = self::singleton();

        if(is_array($arg))
        {
            $result = new stdClass();

            foreach($arg as $k)
            {
                $result->{$k} = $registry->__get($k);
            }
        }
        else
        {
            $result = $registry->__get($arg);
        }

        return $result;
    }

    /**
     * Call overloading
     *
     * @param   string  $name
     */
    public function __call($name, $arguments)
    {
        if(array_key_exists($name, $this->data) == false)
        {
            $this->data[$name] = new self();
        }

        return $this->data[$name];
    }

    /**
     * Load data from XML
     *
     * @param   SimpleXMLElement    $xml
     * @param   array               $data
     * @return  void
     */
    protected static function fromXML($xml, &$data)
    {
        foreach($xml as $k => $v)
        {
            if(count($v) > 0) 
            {
                $data[$k] = new self();
                self::fromXML($v, $data[$k]->data);
            }
            else
            {
                $data[$k] = ((string) $v);
            }
        }
    }
}
