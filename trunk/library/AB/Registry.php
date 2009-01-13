<?php

/**
 * Data registry
 *
 * @category    Autoblog
 * @package     AB
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
     * Registry data
     *
     * @var array
     */
    protected $data = array();

    /**
     * Registry level
     *
     * @var integer
     */
    protected $level;


    /**
     * Registry constructor
     *
     * @param   integer $level
     * @return  void
     */
    private function __construct($level=0)
    {
        $this->level = $level;
    }

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
     * Get overloading
     *
     * @param   string  $name
     * @return  mixed
     */
    public function __get ($name)
    {
        if(array_key_exists($name, $this->data) == false)
        {
            $this->data[$name] = new self($this->level + 1);
        }

        return $this->data[$name];
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
     * To String
     *
     * @return  string
     */
    public function __toString()
    {
        $output = "\n";

        foreach($this->data as $name => $value)
        {
            $output.= str_repeat(" ", $this->level);
            $output.= $name . ": " . $value . "\n";
        }

        return $output;
    }

    /**
     * Is set
     *
     * @param   string  $name
     * @return  boolean
     */
    public function __isset($name)
    {
        return empty($this->data[$name]) ^ true;
    }

    /**
     * Unset
     *
     * @param   string  $name
     * @return  void
     */
    public function __unset($name)
    {
        unset($this->data[$name]);
    }
}
