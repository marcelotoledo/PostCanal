<?php

/**
 * UserProfile model class
 * 
 * @category    Autoblog
 * @package     Model
 */
class UserProfile extends AB_Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table_name = 'user_profile';

    /**
     * Primary key column name
     *
     * @var string
     */
    protected $primary_key = 'user_profile_id';


    /**
     * How many seconds since register last message
     *
     * @return  integer
     */
    public function timeLastRegister()
    {
        $last = empty($this->register_last_message) ? 
                0 : 
                strtotime($this->register_last_message);

        return time() - $last;
    }

    /**
     * How many seconds since recovery last message
     *
     * @return  integer
     */
    public function timeLastRecovery()
    {
        $last = empty($this->recovery_last_message) ? 
                0 : 
                strtotime($this->recovery_last_message);

        return time() - $last;
    }

    /**
     * Find UserProfile with an encapsulated SELECT command
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
     * Get UserProfile with SQL
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

    /**
     * Get UserProfile from email
     *
     * @param   string  $email
     * @return  UserProfile|null
     */
    public static function checkEmail($email)
    {
        return current(self::find(array(
            'login_email' => $email,
            'enabled'     => true)));
    }
}
