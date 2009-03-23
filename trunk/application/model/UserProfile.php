<?php

/**
 * UserProfile model class
 * 
 * @category    Blotomate
 * @package     Model
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
class UserProfile extends B_Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected static $table_name = 'user_profile';

    /**
     * Table structure
     *
     * @var array
     */
    protected static $table_structure = array('user_profile_id'=>array('type'=>'integer','size'=>0,'required'=>false),'login_email'=>array('type'=>'string','size'=>100,'required'=>true),'login_password_md5'=>array('type'=>'string','size'=>32,'required'=>true),'register_confirmation'=>array('type'=>'boolean','size'=>0,'required'=>false),'created_at'=>array('type'=>'date','size'=>0,'required'=>false),'updated_at'=>array('type'=>'date','size'=>0,'required'=>false),'enabled'=>array('type'=>'boolean','size'=>0,'required'=>false),'name'=>array('type'=>'string','size'=>100,'required'=>false),'email_update'=>array('type'=>'string','size'=>100,'required'=>false),'register_message_time'=>array('type'=>'date','size'=>0,'required'=>false),'register_confirmation_time'=>array('type'=>'date','size'=>0,'required'=>false),'recovery_message_time'=>array('type'=>'date','size'=>0,'required'=>false),'created_at'=>array('type'=>'date','size'=>0,'required'=>false),'updated_at'=>array('type'=>'date','size'=>0,'required'=>false));

    /**
     * Sequence name
     *
     * @var string
     */
    protected static $sequence_name = '';

    /**
     * Primary key name
     *
     * @var string
     */
    protected static $primary_key_name = 'user_profile_id';


    /**
     * Set overloading
     *
     * @param   string  $name
     * @param   mixed   $value
     * @return  void
     */
    public function __set ($name, $value)
    {
        /* filters */

        if($name == 'login_email') $value = strtolower($value);

        parent::__set($name, $value);
    }

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
     * Save model
     *
     * @return  boolean
     */
    public function save()
    {
        /* generate Hash */

        if($this->isNew()) 
        {
            $this->hash = L_Utility::randomString(8);
        }

        return parent::save();
    }

    /**
     * Find UserProfile with an encapsulated SELECT command
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
     * Get UserProfile with SQL
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
     * Find UserProfile by primary key
     *
     * @param   integer $id    Primary key value
     *
     * @return  UserProfile|null 
     */
    public static function findByPrimaryKey($id)
    {
        return current(self::find(array(self::$primary_key_name => $id)));
    }

    /**
     * Find UserProfile by primary key and enabled (user_profile_enabled_index)
     * 
     * @param   integer $id    Primary key value
     *
     * @return  UserProfile|null 
     */
    public static function findByPrimaryKeyEnabled($id)
    {
        return current(self::find(
            array(self::$primary_key_name => $id, 'enabled' => true)));
    }

    /**
     * Find UserProfile from email (user_profile_email_index)
     *
     * @param   string  $email
     * @return  UserProfile|null
     */
    public static function findByEmail($email)
    {
        return current(self::find(array(
            'login_email' => strtolower($email),
            'enabled'     => true)));
    }

    /**
     * Find UserProfile from login (user_profile_login_index)
     *
     * @param   string  $email
     * @param   string  $password_md5   md5($password)
     * @return  UserProfile|null
     */
    public static function findByLogin($email, $password_md5)
    {
        return current(self::find(array(
            'login_email'        => strtolower($email),
            'login_password_md5' => $password_md5,
            'enabled'            => true)));
    }

    /**
     * Find UserProfile from Hash
     *
     * @param   string  $email
     * @param   string  $hash
     * @return  UserProfile|null
     */
    public static function findByHash($email, $hash)
    {
        return current(self::find(array(
            'login_email' => strtolower($email), 
            'hash'        => $hash,
            'enabled'     => true)));
    }
}
