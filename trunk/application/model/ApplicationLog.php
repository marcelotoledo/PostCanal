<?php

/**
 * ApplicationLog model class
 * 
 * @category    Blotomate
 * @package     Model
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
class ApplicationLog extends AB_Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected static $table_name = 'application_log';

    /**
     * Table structure
     *
     * @var string|array
     */
    protected $table_structure = 'a:8:{s:18:"application_log_id";a:3:{s:1:"t";s:1:"i";s:1:"s";i:0;s:1:"r";b:0;}s:8:"priority";a:3:{s:1:"t";s:1:"i";s:1:"s";i:0;s:1:"r";b:0;}s:7:"message";a:3:{s:1:"t";s:1:"s";s:1:"s";i:0;s:1:"r";b:1;}s:6:"method";a:3:{s:1:"t";s:1:"s";s:1:"s";i:100;s:1:"r";b:0;}s:10:"controller";a:3:{s:1:"t";s:1:"s";s:1:"s";i:100;s:1:"r";b:0;}s:6:"action";a:3:{s:1:"t";s:1:"s";s:1:"s";i:100;s:1:"r";b:0;}s:15:"user_profile_id";a:3:{s:1:"t";s:1:"i";s:1:"s";i:0;s:1:"r";b:0;}s:10:"created_at";a:3:{s:1:"t";s:1:"d";s:1:"s";i:0;s:1:"r";b:0;}}';

    /**
     * Sequence name
     *
     * @var string
     */
    protected static $sequence_name = 'application_log_seq';

    /**
     * Primary key name
     *
     * @var string
     */
    protected static $primary_key_name = 'application_log_id';


    /**
     * Get table name
     *
     * @return  string
     */
    public function getTableName()
    {
        return self::$table_name;
    }

    /**
     * Get table structure
     *
     * @return  array
     */
    public function getTableStructure()
    {
        if(!is_array($this->table_structure))
        {
            $this->table_structure = unserialize($this->table_structure);
        }

        return $this->table_structure;
    }

    /**
     * Get sequence name
     *
     * @return  string
     */
    public function getSequenceName()
    {
        return self::$sequence_name;
    }

    /**
     * Get primary key name
     *
     * @return  string
     */
    public function getPrimaryKeyName()
    {
        return self::$primary_key_name;
    }

    /**
     * Find ApplicationLog with an encapsulated SELECT command
     *
     * @param   array   $conditions WHERE parameters
     * @param   array   $order      ORDER parameters
     * @param   integer $limit      LIMIT parameter
     * @param   integer $offset     OFFSET parameter
     * @return  array
     */
    public static function find ($conditions=array(), 
                                 $order=array(), 
                                 $limit=0, 
                                 $offset=0)
    {
        return parent::_find($conditions, 
                             $order, 
                             $limit, 
                             $offset, 
                             self::$table_name,
                             get_class());
    }

    /**
     * Get ApplicationLog with SQL
     *
     * @param   string  $sql    SQL query
     * @param   array   $data   values array
     * @return  array
     */
    public static function selectModel ($sql, $data=array())
    {
        return parent::_selectModel($sql, $data, get_class());
    }

    /**
     * Execute a SQL insert query and returns last insert id
     *
     * @param   string  $sql        SQL query
     * @param   array   $data       values array
     * @return  integer
     */
    public static function insert($sql, $data=array())
    {
        return parent::_insert($sql, $data, self::sequence_name);
    }

    /**
     * Find ApplicationLog by primary key
     *
     * @param   integer $id    Primary key value
     *
     * @return  ApplicationLog|null 
     */
    public static function findByPrimaryKey($id)
    {
        return current(self::find(array(self::$primary_key_name => $id)));
    }
}
