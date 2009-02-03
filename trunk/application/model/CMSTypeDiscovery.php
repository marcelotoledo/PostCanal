<?php

/* CMS type discovery examples:

        NAME                VALUE

        url                 http:\/\/.+\.wordpress\.com
        header              server:\ wordpress\.com
        dom                 div#wrapper
        dom                 meta[@name='generator'][@content^='WordPress']
*/

/**
 * CMSTypeDiscovery model class
 * 
 * @category    Blotomate
 * @package     Model
 */
class CMSTypeDiscovery extends AB_Model
{
    /**
     * Discovery name constants
     */
    const NAME_URL    = "url";
    const NAME_HEADER = "header";
    const NAME_DOM    = "dom";


    /**
     * Table name
     *
     * @var string
     */
    protected static $table_name = 'cms_type_discovery';

    /**
     * Sequence name
     *
     * @var string
     */
    protected static $sequence_name = 'cms_type_discovery_seq';

    /**
     * Primary key name
     *
     * @var string
     */
    protected static $primary_key_name = 'cms_type_discovery_id';


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
     * Save model
     *
     * @return  boolean
     */
    public function save()
    {
        return parent::_save(self::$sequence_name);
    }

    /**
     * Find CMSTypeDiscovery with an encapsulated SELECT command
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
     * Get CMSTypeDiscovery with SQL
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
     * Find CMSTypeDiscovery by primary key
     *
     * @param   integer $id    Primary key value
     *
     * @return  CMSTypeDiscovery|null 
     */
    public static function findByPrimaryKey($id)
    {
        return current(self::find(array(self::$primary_key_name => $id)));
    }

    /**
     * Find CMSTypeDiscovery by name [and value [regexp]]
     *
     * @param   string  $name
     * @param   string  $value
     * @param   boolean $regexp
     *
     * @return  array
     */
    public static function findByNameValue($name, $value=null, $regexp=false)
    {
        $sql = " SELECT * FROM " . self::$table_name;
        $sql.= " WHERE name = ? ";
        $conditions = array();
        $conditions[] = $name;

        if(!empty($value)) 
        {
            $sql.= " AND ? " . ($regexp ? '~*' : '=') . " value ";
            $conditions[] = $value;
        }

        $sql.= " ORDER BY cms_type_id ASC";
        
        return self::_selectModel($sql, array_values($conditions), get_class());
    }
}
