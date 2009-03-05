<?php

/**
 * Data registry
 *
 * Generic storage class helps to manage global data
 *
 * @category    Blotomate
 * @package     AB
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
class AB_Registry
{
    /**
     * Singleton instance
     * 
     * @var AB_Registry
     */
    private static $instance;

    /**
     * Data
     * 
     * @var array
     */
    private $data = array();

    
    private function __construct() { }
    private function __clone() { }

    /**
     * Singleton constructor
     * 
     * @return AB_Dispatcher
     */
    public static function singleton()
    {
        if(is_null(self::$instance) == true)
        {
            self::$instance = new self();
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
     * Get overloading
     * 
     * @param   string  $name
     * @return  mixed
     */
    public function __get ($name)
    {
        if(array_key_exists($name, $this->data) == false)
        {
            $this->data[$name] = new self();
        }

        return $this->data[$name];
    }

    /**
     * Check if a node is set
     *
     * @param   string  $name
     * @return  boolean
     */
    public function __isset($name)
    {
        $result = false;

        if(array_key_exists($name, $this->data))
        {
            $result = true;

            if(is_object($this->data[$name]))
            {
                if(get_class($this->data[$name]) == __CLASS__)
                {
                    $result = false;
                }
            }
        }

        return $result;
    }

    /**
     * Check if a node is set (from array path)
     *
     * @param   array   $path
     * @return  boolean
     */
    public function check($path)
    {
        $b = false;
        $n = $this;

        foreach($path as $i)
        {
            if(array_key_exists($i, $n->data))
            {
                $n = $n->data[$i];
            }
            else
            {
                $n = null;
                break;
            }
        }
        
        return isset($n);
    }

    /**
     * Return null string to AB_Registry objects
     *
     * @return  string
     */
    public function __toString()
    {
        return ((string) null);
    }

    /**
     * Load data from file
     *
     * @param   string  $filename
     * @param   string  $type
     * @return  void
     */
    public function load($filename, $type='xml')
    {
        if(file_exists($filename))
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
