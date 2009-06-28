<?php

/**
 * Base Translation
 * 
 * @category    PostCanal
 * @package     Base Library
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
            $value = str_replace('_', ' ', $name);
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
     * @param   mixed   $template   Template name or array
     * @return  void
     */
    public function load($template)
    {
        $sql = "SELECT name, value FROM " . self::$table_name . " WHERE (" .
               substr(str_repeat("template = ? OR ", count($template)), 0, -4) .
               ") AND culture = ?";

        $arg = is_array($template) ? $template : array($template);
        $arg[] = $this->culture;

        foreach(B_Model::select($sql, $arg) as $_t)
        {
            $this->data[$_t->name] = $_t->value;
        }
    }
}
