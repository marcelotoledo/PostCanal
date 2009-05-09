<?php

/**
 * UserBlogFeed model class
 * 
 * @category    Blotomate
 * @package     Application Model
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
    protected static $table_structure = array (
		'user_blog_feed_id' => array ('type' => 'integer','size' => 0,'required' => false),
		'user_blog_id' => array ('type' => 'integer','size' => 0,'required' => true),
		'aggregator_feed_id' => array ('type' => 'integer','size' => 0,'required' => true),
		'hash' => array ('type' => 'string','size' => 8,'required' => true),
		'feed_title' => array ('type' => 'string','size' => 100,'required' => true),
		'feed_description' => array ('type' => 'string','size' => 0,'required' => true),
		'ordering' => array ('type' => 'integer','size' => 0,'required' => false),
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
        /* generate hash */

        if($this->isNew()) 
        {
            $this->hash = A_Utility::randomString(8);
            $this->increaseOrdering();
        }

        return parent::save();
    }

    /**
     * Increase feeds ordering
     */
    public function increaseOrdering()
    {
        if($this->user_blog_id)
        {
            $sql = "UPDATE " . self::$table_name . " " . 
                   "SET ordering=(ordering+1) " .
                   "WHERE user_blog_id = ?";
            self::execute($sql, array($this->user_blog_id));
        }
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
     * Get UserBlogFeed by primary key
     *
     * @param   integer $id    Primary key value
     *
     * @return  UserBlogFeed|null 
     */
    public static function getByPrimaryKey($id)
    {
        return current(self::find(array(self::$primary_key_name => $id)));
    }

    /**
     * Get by User Blog and Aggregator Feed
     *
     * @param   integer     $blog_id    User Blog ID
     * @param   integer     $feed_id    Aggregator Feed ID
     *
     * @return  UserBlogFeed|null
     */
    public static function getByBlogAndFeed($blog_id, $feed_id)
    {
        return current(self::find(array(
            'user_blog_id' => $blog_id,
            'aggregator_feed_id' => $feed_id
        )));
    }

    /**
     * Get by Blog and hash
     *
     * @param   integer $blog_id
     * @param   string  $hash
     * @return  UserBlog|null
     */
    public static function getByBlogAndHash($blog_id, $hash)
    {
        return current(self::find(array(
            'user_blog_id' => $blog_id,
            'hash' => $hash)));
    }

    /**
     * Partial by User Blog
     *
     * @param   string      $blog_hash
     * @param   integer     $user_id
     *
     * @return  array
     */
    public static function partialByBlogAndUser($blog_hash, $user_id)
    {
        $sql = "SELECT a.hash as feed, b.feed_url, a.feed_title,
                       a.feed_description, b.feed_update_time,
                       b.feed_status, a.ordering
                FROM " . self::$table_name . " AS a
                LEFT JOIN model_aggregator_feed AS b
                ON (a.aggregator_feed_id = b.aggregator_feed_id)
                WHERE a.user_blog_id = (
                    SELECT user_blog_id
                    FROM model_user_blog
                    WHERE hash = ? AND user_profile_id = ?)
                AND b.enabled = 1
                ORDER BY a.ordering ASC, a.created_at DESC";
        
        return self::select($sql, array($blog_hash, $user_id));
    }

    /**
     * Get feed articles for a user blog feed
     *
     * @param   string      $blog_hash
     * @param   integer     $user_id
     * @param   integer     $feed_hash
     * @param   integer     $start_time
     * @param   integer     $limit
     *
     * @return  array
     */
    public static function partialArticles($blog_hash, 
                                           $user_id, 
                                           $feed_hash,
                                           $start_time=null, 
                                           $limit=25)
    {
        if(!$start_time) $start_time = time();

        $sql = "SELECT item_md5 AS article, item_title AS title, item_link AS link, 
                       item_date AS date, item_author AS author, item_content AS content
                FROM model_user_blog_feed AS a
                LEFT JOIN model_aggregator_feed_item AS b
                ON (a.aggregator_feed_id = b.aggregator_feed_id)
                WHERE a.enabled = true AND b.item_date < ? 
                AND a.hash = ? AND a.user_blog_id = (
                    SELECT user_blog_id
                    FROM model_user_blog
                    WHERE hash = ? AND user_profile_id = ?) 
                ORDER BY b.item_date DESC, b.created_at DESC LIMIT " . intval($limit);

        return self::select($sql, array(date("Y-m-d H:i:s", $start_time), 
                                        $feed_hash,
                                        $blog_hash, 
                                        $user_id));
    }

    /**
     * Get feed articles associated with user blog feeds
     *
     * @param   string      $blog_hash
     * @param   integer     $user_id
     * @param   integer     $start_time
     * @param   integer     $limit
     *
     * @return  array
     */
    public static function partialArticlesAll($blog_hash, 
                                              $user_id, 
                                              $start_time=null, 
                                              $limit=50)
    {
        if(!$start_time) $start_time = time();

        $sql = "SELECT a.feed_title AS feed, b.item_md5 AS article, 
                       b.item_title AS title, b.item_link AS link, 
                       b.item_date AS date, b.item_author AS author, 
                       b.item_content AS content
                FROM model_user_blog_feed AS a 
                LEFT JOIN model_aggregator_feed_item AS b 
                ON (a.aggregator_feed_id = b.aggregator_feed_id) 
                WHERE a.enabled = true AND b.item_date < ? AND a.user_blog_id = (
                    SELECT user_blog_id
                    FROM model_user_blog
                    WHERE hash = ? AND user_profile_id = ?) 
                ORDER BY b.item_date DESC, b.created_at DESC LIMIT " . intval($limit);

        return self::select($sql, array(date("Y-m-d H:i:s", $start_time), 
                                        $blog_hash, 
                                        $user_id));
    }

    /**
     * Update feed ordering
     *
     * @param   string      $blog_hash
     * @param   integer     $user_id        
     * @param   string      $feed_hash
     * @param   integer     $ordering
     */
    public static function updateOrdering($blog_hash, $user_id, $feed_hash, $ordering)
    {
        $sql = "SELECT * FROM model_user_blog_feed WHERE user_blog_id = (SELECT user_blog_id FROM model_user_blog WHERE hash = ? AND user_profile_id = ?) AND hash = ?";
        $cur = current(self::selectModel($sql, array($blog_hash, $user_id, $feed_hash)));

        $i = $cur->ordering;
        $j = $ordering;
        $k = ($j < $i);

        if($j == $i) return null;

        $sql = "UPDATE model_user_blog_feed SET ";
        
        if($k)
        {
            $sql.= "ordering=(ordering+1) ";
        }
        else
        {
            $sql.= "ordering=(ordering-1) ";
        }
        
        $sql.= "WHERE user_blog_id = (SELECT user_blog_id FROM model_user_blog WHERE hash = ? AND user_profile_id = ?) ";
        $data = array($blog_hash, $user_id);

        if($k)
        {
            $sql.= "AND ordering >= ? AND ordering < ?";
            $data = array_merge($data, array($j, $i));
        }
        else
        {
            $sql.= "AND ordering > ? AND ordering <= ?";
            $data = array_merge($data, array($i, $j));
        }

        self::execute($sql, $data);

        $cur->ordering = $j;
        $cur->save();
    }
}
