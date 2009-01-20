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
     * Sequence name
     *
     * @var string
     */
    protected $sequence_name = 'user_profile_seq';

    /**
     * Primary key column name
     *
     * @var string
     */
    protected $primary_key = 'user_profile_id';

    /**
     * UID base
     *
     * @var string
     */
    private static $uid_base = 'T6HCN9PtMz7BQrZbS3R4mAxJKDqFXG8EckLV';


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
     * Execute a SQL insert query and returns last insert id
     *
     * @param   string  $sql        SQL query
     * @param   array   $data       values array
     * @return  integer
     */
    public static function insert($sql, $data=array())
    {
        $class_name = get_class();
        $class_object = new $class_name();

        return $class_object->_insert($sql, $data);
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

        parent::save();
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
            $_md5 = md5($this->user_profile_id . ":" . $this->login_email);
            $uid = self::encodeUID($_md5);
        }   

        return $uid;
    }

    /**
     * Get UserProfile from email
     *
     * @param   string  $email
     * @return  UserProfile|null
     */
    public static function getFromEmail($email)
    {
        return current(self::find(array(
            'login_email' => $email,
            'enabled'     => true)));
    }

    /**
     * Get UserProfile from login
     *
     * @param   string  $email
     * @param   string  $password_md5   md5($password)
     * @return  UserProfile|null
     */
    public static function getFromLogin($email, $password_md5)
    {
        return current(self::find(array(
            'login_email'        => $email,
            'login_password_md5' => $password_md5,
            'enabled'            => true)));
    }

    /**
     * Get UserProfile from UID
     *
     * @param   string  $uid
     * @return  UserProfile|null
     */
    public static function getFromUID($uid)
    {
        if(empty($uid)) return null;

        $_md5 = self::decodeUID($uid);

        if(empty($_md5)) return null;

        $class_name = get_class();
        $class_object = new $class_name();

        $sql = "SELECT * FROM " . $class_object->table_name . " " . 
               "WHERE MD5(user_profile_id || ':' || login_email) = ?";
        
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
        if(!eregi('^[0-9a-f]{32}$', $_md5))
        {
            return null;
        }

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

        for($i = 0; $i < 40; $i++)
            $out.= $b[base_convert($x[$i], 36, 10)];

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
        if(!eregi('^[0-9a-z]{40}$', $uid))
        {
            return null;
        }

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

        for($i=0;$i<8;$i++)
            if($h[($i*4)] != $h[$i + 32]) $c = false;

        return ($c == true) ? substr($h, 0, 32) : null;
    }
}
