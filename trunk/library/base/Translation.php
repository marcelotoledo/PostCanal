<?php

/**
 * Translation
 * 
 * @category    Blotomate
 * @package     Base
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
class B_Translation
{
    /**
     * Culture
     * 
     * @var string
     */
    private $culture;

    /**
     * Data
     * 
     * @var array
     */
    private $data = array();

    /**
     * Translation table name
     *
     * @var string
     */
    private static $table_name = 'base_translation';


    /**
     * Translation constructor
     *
     * @param   string      $controller
     * @param   string      $action
     * @param   string      $culture
     * @return  void
     */
    public function __construct($culture='us_EN')
    {
        $this->culture = $culture;
    }

    /**
     * Get overloading
     *
     * @param   string  $name
     * @return  mixed
     */
    public function __get($name)
    {
        $value = null;

        if(is_array($this->data))
        {
            if(array_key_exists($name, $this->data))
            {
                $value = $this->data[$name];
            }
        }

        if($value == null)
        {
            $value = $name;
        }

        return $value;
    }

    /**
     * Set overloading
     *
     * @param   string  $name
     * @param   mixed   $value
     * @return  void
     */
    public function __set($name, $value)
    {
        if(is_array($this->data) == false)
        {
            $this->data = array();
        }

        $this->data[$name] = $value;
    }

    /**
     * Load translation data
     *
     * @param   string  $template
     * @return  void
     */
    public function load($template)
    {
        $result = B_Model::select("SELECT name, value FROM " . self::$table_name . " " .
                                   "WHERE template = ? AND culture = ?", 
                                   array($template, $this->culture));

        foreach($result as $i) $this->data[$i->name] = $i->value;
    }
}
