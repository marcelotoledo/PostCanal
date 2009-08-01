<?php

/**
 * UserBlog model class
 * 
 * @category    PostCanal
 * @package     Application Model
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class UserBlog extends B_Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected static $table_name = 'model_user_blog';

    /**
     * Table structure
     *
     * @var array
     */
    protected static $table_structure = array (
		'user_blog_id' => array ('type' => 'integer','size' => 0,'required' => false),
		'user_profile_id' => array ('type' => 'integer','size' => 0,'required' => true),
		'blog_type_id' => array ('type' => 'integer','size' => 0,'required' => true),
		'blog_type_revision' => array ('type' => 'integer','size' => 0,'required' => false),
		'hash' => array ('type' => 'string','size' => 8,'required' => true),
		'name' => array ('type' => 'string','size' => 200,'required' => true),
		'blog_url' => array ('type' => 'string','size' => 0,'required' => true),
		'blog_manager_url' => array ('type' => 'string','size' => 0,'required' => true),
		'blog_username' => array ('type' => 'string','size' => 255,'required' => false),
		'blog_password' => array ('type' => 'string','size' => 255,'required' => false),
		'enqueueing_auto' => array ('type' => 'integer','size' => 0,'required' => false),
		'enqueueing_auto_updated_at' => array ('type' => 'date','size' => 0,'required' => false),
		'publication_auto' => array ('type' => 'integer','size' => 0,'required' => false),
		'publication_interval' => array ('type' => 'integer','size' => 0,'required' => false),
		'keywords' => array ('type' => 'string','size' => 0,'required' => false),
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
    protected static $primary_key_name = 'user_blog_id';


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
     * Get UserBlog by primary key
     *
     * @param   integer $id    Primary key value
     *
     * @return  UserBlog|null 
     */
    public static function getByPrimaryKey($id)
    {
        return current(self::select("SELECT * FROM " . self::$table_name . 
            " WHERE " . self::$primary_key_name . " = ?", 
            array($id), PDO::FETCH_CLASS, get_class()));
    }

    // -------------------------------------------------------------------------

    public static $allow_write = array 
    (
        'name',
        'blog_username',
        'blog_password',
        'enqueueing_auto',
        'publication_auto',
        'publication_interval',
        'keywords'
    );

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
     * Find Blog by user profile
     *
     * @param   integer         $user_id    User Profile ID
     * @param   boolean|null    $enabled
     * @return  array
     */
    public static function findByUser($user_id, $enabled=null)
    {
        $sql = "SELECT * FROM " . self::$table_name . 
               " WHERE user_profile_id = ?" .
               " AND deleted = 0";
        $args = array($user_id);

        if(is_bool($enabled))
        {
            $sql.= " AND enabled = ?";
            $args[] = $enabled;
        }

        $sql.= " ORDER BY name ASC";

        return self::select($sql, $args);
    }

    /**
     * get Blog from User and Blog Hash
     *
     * @param   integer $user_id    User Profile ID_
     * @param   string  $hash
     * @return  UserBlog|null
     */
    public static function getByUserAndHash($user_id, $hash)
    {
        return current(self::select(
            "SELECT * FROM " . self::$table_name . 
            " WHERE user_profile_id = ? AND hash = ?", 
            array($user_id, $hash), PDO::FETCH_CLASS, get_class()));
    }

    /**
     * Delete (deleted=1) blog by user and hash
     */
    public static function deleteByUserAndHash($user_id, $hash)
    {
        return self::execute('UPDATE ' . self::$table_name . ' SET deleted=1 WHERE user_profile_id=? AND hash=?', array($user_id, $hash));
    }

    /**
     * Select total registered blogs
     */
    public static function total($user_id)
    {
        $q = current(self::select("SELECT COUNT(*) AS total
                                   FROM " . self::$table_name . 
                                 " WHERE user_profile_id=? AND deleted=0",
        array($user_id), PDO::FETCH_OBJ));

        return is_object($q) ? $q->total : 0;
    }
}
