<?php

/**
 * UserCMS model class
 * 
 * @category    Autoblog
 * @package     Model
 */
class UserCMS extends AB_Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected static $table_name = 'user_cms';

    /**
     * Sequence name
     *
     * @var string
     */
    protected static $sequence_name = 'user_cms_seq';

    /**
     * Primary key name
     *
     * @var string
     */
    protected static $primary_key_name = 'user_cms_id';


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
        $current_date = date("Y/m/d H:i:s");

        $this->isNew() ? $this->created_at = $current_date : 
                         $this->updated_at = $current_date;

        return parent::_save(self::$sequence_name);
    }

    /**
     * Find UserCMS with an encapsulated SELECT command
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
     * Get UserCMS with SQL
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
     * Get UserCMS from primary key
     *
     * @param   integer $id    Primary key value
     *
     * @return  UserCMS|null 
     */
    public static function getFromPrimaryKey($id)
    {
        return current(self::find(array(self::$primary_key_name => $id)));
    }
}
