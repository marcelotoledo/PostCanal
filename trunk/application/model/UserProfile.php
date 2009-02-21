<?php

/**
 * UserProfile model class
 * 
 * @category    Blotomate
 * @package     Model
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
class UserProfile extends AB_Model
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
     * @var string|array
     */
    protected $table_structure = 'a:10:{s:15:"user_profile_id";a:3:{s:1:"t";s:1:"i";s:1:"s";i:0;s:1:"r";b:0;}s:11:"login_email";a:3:{s:1:"t";s:1:"s";s:1:"s";i:100;s:1:"r";b:1;}s:18:"login_password_md5";a:3:{s:1:"t";s:1:"s";s:1:"s";i:32;s:1:"r";b:1;}s:21:"register_message_time";a:3:{s:1:"t";s:1:"d";s:1:"s";i:0;s:1:"r";b:0;}s:21:"register_confirmation";a:3:{s:1:"t";s:1:"b";s:1:"s";i:0;s:1:"r";b:0;}s:26:"register_confirmation_time";a:3:{s:1:"t";s:1:"d";s:1:"s";i:0;s:1:"r";b:0;}s:21:"recovery_message_time";a:3:{s:1:"t";s:1:"d";s:1:"s";i:0;s:1:"r";b:0;}s:10:"created_at";a:3:{s:1:"t";s:1:"d";s:1:"s";i:0;s:1:"r";b:0;}s:10:"updated_at";a:3:{s:1:"t";s:1:"d";s:1:"s";i:0;s:1:"r";b:0;}s:7:"enabled";a:3:{s:1:"t";s:1:"b";s:1:"s";i:0;s:1:"r";b:0;}}';

    /**
     * Sequence name
     *
     * @var string
     */
    protected static $sequence_name = 'user_profile_seq';

    /**
     * Primary key name
     *
     * @var string
     */
    protected static $primary_key_name = 'user_profile_id';

    /**
     * UID base
     *
     * @var string
     */
    private static $uid_base = 'T6HCN9PtMz7BQrZbS3R4mAxJKDqFXG8EckLV';


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
     * Save model
     *
     * @return  boolean
     */
    public function save()
    {
        if(!$this->isNew()) $this->updated_at = date("Y/m/d H:i:s");
        $this->login_email = strtolower($this->login_email);

        return parent::save();
    }

    /**
     * Get UID
     *
     * @return  string|null
     */
    public function getUID()
    {
        $uid = null;

        if(!$this->isNew())
        {
            $_md5 = md5($this->user_profile_id . ":" .
                        strtolower($this->login_email) . ":" .
                        $this->login_password_md5);
            $uid = self::encodeUID($_md5);
        }   

        return $uid;
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
     * Find UserProfile by primary key and enabled
     *
     * @param   integer $id    Primary key value
     *
     * @return  UserProfile|null 
     */
    public static function findByPrimaryKeyEnabled($id)
    {
        return current(self::find(array(
            self::$primary_key_name => $id, 
            'enabled'               => true)));
    }

    /**
     * Get UserProfile from email
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
     * Get UserProfile from login
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
     * Get UserProfile from UID
     *
     * @param   string  $uid
     * @return  UserProfile|null
     */
    public static function findByUID($uid)
    {
        if(empty($uid)) return null;

        $_md5 = self::decodeUID($uid);

        if(empty($_md5)) return null;

        $sql = "SELECT * FROM " . self::$table_name . " " . 
               "WHERE MD5(user_profile_id || ':' ||
                          login_email || ':' ||
                          login_password_md5) = ?";
        
        return current(self::selectModel($sql, array($_md5)));
    }

    /**
     * Encode UID
     *
     * @param   string  $_md5
     * @return  string|null
     */
    private static function encodeUID($_md5)
    {
        if(!eregi('^[0-9a-f]{32}$', $_md5)) return null;

        $s = $_md5;

        for($i = 0; $i < 8; $i++) $s.= $s[($i * 4)];

        $x = "";

        for($i = 0; $i < 40; $i += 4)
        {
            $p = substr($s, $i, 4);
            $p = str_pad(base_convert($p, 16, 36), 4, "0", STR_PAD_LEFT);
            $x.= strrev($p);
        }

        $b = self::$uid_base;
        $out = "";

        for($i = 0; $i < 40; $i++) $out.= $b[base_convert($x[$i], 36, 10)];

        return $out;
    }

    /**
     * Decode UID
     *
     * @param   string  $uid
     * @return  string|null
     */
    private static function decodeUID($uid)
    {
        if(!eregi('^[0-9a-z]{40}$', $uid)) return null;

        $s = $uid;
        $b = self::$uid_base;
        $x = "";

        for($i = 0; $i < 40; $i++)
        {
            $k = base_convert(strpos($b, $s[$i]), 10, 36);
            $x.= $k;
        }

        $h = "";

        for($i = 0; $i < 40; $i += 4)
        {
            $p = strrev(substr($x, $i, 4));
            $h.= str_pad(base_convert($p, 36, 16), 4, "0", STR_PAD_LEFT);
        }

        $c = true;

        for($i=0;$i<8;$i++) if($h[($i*4)] != $h[$i + 32]) $c = false;

        return ($c == true) ? substr($h, 0, 32) : null;
    }
}
