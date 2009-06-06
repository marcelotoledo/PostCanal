<?php

/**
 * UserProfile model class
 * 
 * @category    PostCanal
 * @package     Application Model
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class UserProfile extends B_Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected static $table_name = 'model_user_profile';

    /**
     * Table structure
     *
     * @var array
     */
    protected static $table_structure = array (
		'user_profile_id' => array ('type' => 'integer','size' => 0,'required' => false),
		'hash' => array ('type' => 'string','size' => 8,'required' => true),
		'login_email_local' => array ('type' => 'string','size' => 64,'required' => true),
		'login_email_domain' => array ('type' => 'string','size' => 255,'required' => true),
		'login_password_md5' => array ('type' => 'string','size' => 32,'required' => true),
		'name' => array ('type' => 'string','size' => 100,'required' => false),
		'register_confirmation' => array ('type' => 'boolean','size' => 0,'required' => false),
		'update_email_to' => array ('type' => 'string','size' => 0,'required' => false),
		'register_message_time' => array ('type' => 'date','size' => 0,'required' => false),
		'register_confirmation_time' => array ('type' => 'date','size' => 0,'required' => false),
		'last_login_time' => array ('type' => 'date','size' => 0,'required' => false),
		'recovery_message_time' => array ('type' => 'date','size' => 0,'required' => false),
		'recovery_allowed' => array ('type' => 'boolean','size' => 0,'required' => false),
		'update_email_message_time' => array ('type' => 'date','size' => 0,'required' => false),
		'local_culture' => array ('type' => 'string','size' => 0,'required' => false),
		'local_timezone' => array ('type' => 'string','size' => 0,'required' => false),
		'created_at' => array ('type' => 'date','size' => 0,'required' => false),
		'updated_at' => array ('type' => 'date','size' => 0,'required' => false),
		'enabled' => array ('type' => 'boolean','size' => 0,'required' => false));

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
    protected static $primary_key_name = 'user_profile_id';


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

    // -------------------------------------------------------------------------

    public static $allow_write = array 
    (
        'name'
    );

    /**
     * Get UserProfile by primary key
     *
     * @param   integer $id    Primary key value
     *
     * @return  UserProfile|null 
     */
    public static function getByPrimaryKey($id, $enabled=true)
    {
        return current(self::select(
            "SELECT * FROM " . self::$table_name . 
            " WHERE " . self::$primary_key_name . " = ? AND enabled = ?", 
            array($id, $enabled), PDO::FETCH_CLASS, get_class()));
    }

    /**
     * Set overloading
     */
    public function __set ($name, $value)
    {
        if($name == 'login_email')
        {
            if(strpos($value, '@')==0) return null;
            list($local, $domain) = explode('@', strtolower($value));
            parent::__set('login_email_local', $local);
            parent::__set('login_email_domain', $domain);
        }
        else
        {
            parent::__set($name, $value);
        }
    }

    /**
     * Get overloading
     */
    public function __get ($name)
    {
        $res = null;

        if($name == 'login_email')
        {
            $res = parent::__get('login_email_local');
            $res.= "@";
            $res.= parent::__get('login_email_domain');
        }
        else
        {
            $res = parent::__get($name);
        }

        return $res;
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
            $this->hash = A_Utility::randomString(8);
        }

        return parent::save();
    }

    /**
     * Get UserProfile from email
     *
     * @param   string  $email
     * 
     * @return  UserProfile|null
     */
    public static function getByEmail($email)
    {
        if(strpos($email, '@')==0) return null;
        list($local, $domain) = explode('@', strtolower($email));

        return current(self::select(
            "SELECT * FROM " . self::$table_name . 
            " WHERE login_email_local = ? AND login_email_domain = ? AND enabled = ?", 
            array($local, $domain, true), PDO::FETCH_CLASS, get_class()));
    }

    /**
     * Get UserProfile from login
     *
     * @param   string  $email
     * @param   string  $password_md5   md5($password)
     * 
     * @return  UserProfile|null
     */
    public static function getByLogin($email, $password_md5)
    {
        if(strpos($email, '@')==0) return null;
        list($local, $domain) = explode('@', strtolower($email));

        return current(self::select(
            "SELECT * FROM " . self::$table_name . 
            " WHERE login_email_local = ? AND login_email_domain = ? " .
            " AND login_password_md5 = ? AND enabled = ?", 
            array($local, $domain, $password_md5, true), PDO::FETCH_CLASS, get_class()));
    }

    /**
     * Get UserProfile from Hash
     *
     * @param   string  $email
     * @param   string  $hash
     * 
     * @return  UserProfile|null
     */
    public static function getByHash($email, $hash)
    {
        if(strpos($email, '@')==0) return null;
        list($local, $domain) = explode('@', strtolower($email));

        return current(self::select(
            "SELECT * FROM " . self::$table_name . 
            " WHERE login_email_local = ? AND login_email_domain = ? " .
            " AND hash = ? AND enabled = ?", 
            array($local, $domain, $hash, true), PDO::FETCH_CLASS, get_class()));
    }
}
