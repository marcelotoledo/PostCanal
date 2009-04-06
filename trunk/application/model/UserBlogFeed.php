<?php

/**
 * UserBlogFeed model class
 * 
 * @category    Blotomate
 * @package     Model
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
class UserBlogFeed extends B_Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected static $table_name = 'model_user_blog_feed';

    /**
     * Table structure
     *
     * @var array
     */
    protected static $table_structure = array();

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
    protected static $primary_key_name = 'user_blog_feed_id';


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
        /* generate CH */

        if($this->isNew()) 
        {
            $this->hash = L_Utility::randomString(8);
        }

        return parent::save();
    }

    /**
     * Find UserBlogFeed with an encapsulated SELECT command
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
     * Get UserBlogFeed with SQL
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
     * Find UserBlogFeed by primary key
     *
     * @param   integer $id    Primary key value
     *
     * @return  UserBlogFeed|null 
     */
    public static function findByPrimaryKey($id)
    {
        return current(self::find(array(self::$primary_key_name => $id)));
    }

    /**
     * Find by User Blog (and Aggregator Feed)
     *
     * @param   integer     $id         UserBlog ID
     * @param   integer     $feed_id    AggregatorFeed ID
     *
     * @return  UserBlogFeed|null 
     */
    public static function findByBlog($id, $feed_id=null)
    {
        $params = array();
        $params['user_blog_id'] = $id;
        if($feed_id != null) $params['aggregator_feed_id'] = $feed_id;
        return self::find($params);
    }

    /**
     * Find by Aggregator Feed
     *
     * @param   integer     $blog_id    UserBlog ID
     * @param   integer     $id         AggregatorFeed ID
     *
     * @return  UserBlogFeed|null 
     */
    public static function findByFeed($blog_id, $id)
    {
        return current(self::findByBlog($blog_id, $id));
    }
}