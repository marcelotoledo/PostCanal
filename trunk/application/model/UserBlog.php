<?php

/**
 * UserBlog model class
 * 
 * @category    Blotomate
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
		'user_blog_id' => array ('type' => 'integer','size' => 0, 'required' => false),
		'user_profile_id' => array ('type' => 'integer','size' => 0,'required' => true),
		'blog_type_id' => array ('type' => 'integer','size' => 0,'required' => true),
		'blog_type_revision' => array ('type' => 'integer','size' => 0,'required' => false),
		'hash' => array ('type' => 'string','size' => 8,'required' => true),
		'name' => array ('type' => 'string','size' => 100,'required' => true),
		'url' => array ('type' => 'string','size' => 200,'required' => true),
		'manager_url' => array ('type' => 'string','size' => 200,'required' => true),
		'manager_username' => array ('type' => 'string','size' => 100,'required' => true),
		'manager_password' => array ('type' => 'string','size' => 100,'required' => true),
		'created_at' => array ('type' => 'date','size' => 0,'required' => false),
		'updated_at' => array ('type' => 'date','size' => 0,'required' => false),
		'enabled' => array ('type' => 'boolean','size' => 0,'required' => false));

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
     * Find UserBlog with an encapsulated SELECT command
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
     * Get UserBlog with SQL
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
        return parent::_insert($sql, $data, self::$sequence_name);
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
        return current(self::find(array(self::$primary_key_name => $id)));
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
        $args = array();
        $args['user_profile_id'] = $user_id;
        if(is_bool($enabled)) $args['enabled'] = $enabled;
        return self::find($args, array('name ASC, created_at DESC'));
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
        return current(self::find(array(
            'user_profile_id' => $user_id,
            'hash' => $hash)));
    }
}
