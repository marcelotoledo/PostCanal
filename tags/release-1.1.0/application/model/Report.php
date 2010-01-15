<?php

/**
 * Report model class
 * 
 * @category    PostCanal
 * @package     Application Model
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class Report extends B_Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected static $table_name = 'application_report';

    /**
     * Table structure
     *
     * @var array
     */
    protected static $table_structure = array (
		'id' => array ('type' => 'integer','size' => 0,'required' => false),
		'name' => array ('type' => 'string','size' => 200,'required' => true),
		'db' => array ('type' => 'string','size' => 200,'required' => false),
		'query' => array ('type' => 'string','size' => 0,'required' => true),
		'enabled' => array ('type' => 'boolean','size' => 0,'required' => false),
		'created_at' => array ('type' => 'date','size' => 0,'required' => false),
		'updated_at' => array ('type' => 'date','size' => 0,'required' => false));

    /**
     * Sequence name
     *
     * @var string
     */
    protected static $sequence_name = null;

    /**
     * Primary key name
     *
     * @var string
     */
    protected static $primary_key_name = 'id';


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
        return self::$table_structure;
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
     * Execute a SQL insert query and returns last insert id
     *
     * @param   string  $sql        SQL query
     * @param   array   $data       values array
     * @return  integer
     */
    public static function insert($sql, $data=array())
    {
        return parent::insert_($sql, $data, self::$sequence_name);
    }

    /**
     * Get Report by primary key
     *
     * @param   integer $id    Primary key value
     *
     * @return  Report|null 
     */
    public static function getByPrimaryKey($id)
    {
        return current(self::select(
            "SELECT * FROM " . self::$table_name . 
            " WHERE " . self::$primary_key_name . " = ?", 
            array($id), PDO::FETCH_CLASS, get_class()));
    }

    // -------------------------------------------------------------------------

    /**
     * Find all enabled reports
     */
    public static function findAll()
    {
        return self::select("SELECT id, name FROM " . self::$table_name . " WHERE enabled=1 ORDER BY name ASC");
    }
}
