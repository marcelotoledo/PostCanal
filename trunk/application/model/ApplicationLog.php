<?php

/**
 * ApplicationLog model class
 * 
 * @category    Autoblog
 * @package     Model
 */
class ApplicationLog extends AB_Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table_name = 'application_log';

    /**
     * Primary key column name
     *
     * @var string
     */
    protected $primary_key = 'application_log_id';


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
        $class_name = get_class();
        $class_object = new $class_name();

        return $class_object->_find($conditions, $order, $limit, $offset);
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
        $class_name = get_class();
        $class_object = new $class_name();

        return $class_object->_selectModel($sql, $data);
    }
}
