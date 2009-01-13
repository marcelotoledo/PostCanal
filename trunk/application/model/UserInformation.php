<?php

/**
 * UserInformation model class
 * 
 * @category    Autoblog
 * @package     Model
 */
class UserInformation extends AB_Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table_name = 'user_information';

    /**
     * Primary key column name
     *
     * @var string
     */
    protected $primary_key = 'user_information_id';


    /**
     * Find UserInformation with an encapsulated SELECT command
     *
     * @param   array    WHERE parameters
     * @param   array         ORDER parameters
     * @param   integer       LIMIT parameter
     * @param   integer      OFFSET parameter
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
     * Get UserInformation with SQL
     *
     * @param   string      SQL query
     * @param   array      values array
     * @return  array
     */
    public static function selectModel ($sql, $data=array())
    {
        $class_name = get_class();
        $class_object = new $class_name();

        return $class_object->_selectModel($sql, $data);
    }
}