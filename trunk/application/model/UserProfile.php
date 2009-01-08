<?php

class UserProfile extends AB_Model
{
    protected $table_name = 'user_profile';
    protected $primary_key = 'user_profile_id';


    public static function find (
        $conditions=array(), $order=array(), $limit=0, $offset=0)
    {
        $class_name = get_class();
        $class_object = new $class_name();

        return $class_object->_find($conditions, $order, $limit, $offset);
    }

    public static function selectModel ($sql, $data=array())
    {
        $class_name = get_class();
        $class_object = new $class_name();

        return $class_object->_selectModel($sql, $data);
    }
}
