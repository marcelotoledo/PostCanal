<?php

/**
 * ProfileInvitation model class
 * 
 * @category    PostCanal
 * @package     Application Model
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class ProfileInvitation extends B_Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected static $table_name = 'model_user_profile_invitation';

    /**
     * Table structure
     *
     * @var array
     */
    protected static $table_structure = array (
		'user_profile_invitation_id' => array ('type' => 'integer','size' => 0,'required' => false),
		'invitation_email_local' => array ('type' => 'string','size' => 64,'required' => true),
		'invitation_email_domain' => array ('type' => 'string','size' => 255,'required' => true),
		'name' => array ('type' => 'string','size' => 100,'required' => true),
		'created_at' => array ('type' => 'date','size' => 0,'required' => false),
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
    protected static $primary_key_name = 'user_profile_invitation_id';


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

    /**
     * Get ProfileInvitation by primary key
     *
     * @param   integer $id    Primary key value
     *
     * @return  ProfileInvitation|null 
     */
    public static function getByPrimaryKey($id)
    {
        return current(self::select(
            "SELECT * FROM " . self::$table_name . 
            " WHERE " . self::$primary_key_name . " = ?", 
            array($id), PDO::FETCH_CLASS, get_class()));
    }

    // -------------------------------------------------------------------------

    /**
     * Set overloading
     */
    public function __set ($name, $value)
    {
        if($name == 'invitation_email')
        {
            if(strpos($value, '@')==0) return null;
            list($local, $domain) = explode('@', strtolower($value));
            parent::__set('invitation_email_local', $local);
            parent::__set('invitation_email_domain', $domain);
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

        if($name == 'invitation_email')
        {
            $res = parent::__get('invitation_email_local');
            $res.= "@";
            $res.= parent::__get('invitation_email_domain');
        }
        else
        {
            $res = parent::__get($name);
        }

        return $res;
    }

    /**
     * Get Invitation from email
     *
     * @param   string  $email
     * 
     * @return  ProfileInvitation|null
     */
    public static function getByEmail($email)
    {
        if(strpos($email, '@')==0) return null;
        list($local, $domain) = explode('@', strtolower($email));

        return current(self::select(
            "SELECT * FROM " . self::$table_name . 
            " WHERE invitation_email_local = ? 
              AND invitation_email_domain = ?", 
            array($local, $domain), PDO::FETCH_CLASS, get_class()));
    }
}
