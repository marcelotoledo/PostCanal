<?php

/**
 * QueueItem model class
 * 
 * @category    Blotomate
 * @package     Model
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
class QueueItem extends B_Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected static $table_name = 'model_user_blog_queue_item';

    /**
     * Table structure
     *
     * @var array
     */
    protected static $table_structure = array (
		'user_blog_queue_item_id' => array ('type' => 'integer','size' => 0, 'required' => false),
		'aggregator_feed_item_id' => array ('type' => 'integer','size' => 0,'required' => true),
		'user_blog_id' => array ('type' => 'integer','size' => 0,'required' => true),
		'hash' => array ('type' => 'string','size' => 8,'required' => true),
		'item_title' => array ('type' => 'string','size' => 0,'required' => true),
		'item_content' => array ('type' => 'string','size' => 0,'required' => true),
		'created_at' => array ('type' => 'date','size' => 0,'required' => false),
		'updated_at' => array ('type' => 'date','size' => 0,'required' => false));

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
    protected static $primary_key_name = 'user_blog_queue_item_id';


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
            $this->hash = L_Utility::randomString(8);
        }

        return parent::save();
    }

    /**
     * Find QueueItem with an encapsulated SELECT command
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
     * Get QueueItem with SQL
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
     * Find QueueItem by primary key
     *
     * @param   integer $id    Primary key value
     *
     * @return  QueueItem|null 
     */
    public static function findByPrimaryKey($id)
    {
        return current(self::find(array(self::$primary_key_name => $id)));
    }

    /**
     * Copy item to queue from feed item
     *
     * @param   string  $feed_item_md5
     * @param   string  $blog_hash
     * @param   string  $feed_hash
     * @param   string  $user_profile_id
     *
     * @return  QueueItem
     */ 
    public static function newFromFeedItem($feed_item_md5, 
                                           $blog_hash, 
                                           $feed_hash, 
                                           $user_profile_id)
    {
        /* get blog */

        if(!is_object(($blog = UserBlog::findByHash($user_profile_id, $blog_hash))))
        {
            $_m = "invalid user blog from hash (" . $blog_hash . ")";
            $_i = $user_profile_id;                          
            $_d = array('method' => __METHOD__, 'user_profile_id' => $_i);
            throw new B_Exception($_m, E_USER_WARNING, $_d);
        }

        $blog_id = $blog->user_blog_id;

        /* get feed */

        if(!is_object(($feed = UserBlogFeed::findByHash($blog_id, $feed_hash))))
        {
            $_m = "invalid user blog feed from blog id (" . $blog_id . ") " .
                  "and hash (" . $feed_hash . ")";
            $_i = $user_profile_id;                          
            $_d = array('method' => __METHOD__, 'user_profile_id' => $_i);
            throw new B_Exception($_m, E_USER_WARNING, $_d);
        }

        $feed_id = $feed->aggregator_feed_id;

        /* get feed item */

        if(!is_object(($feed_item = AggregatorFeedItem::findByItemMD5($feed_id, $feed_item_md5))))
        {
            $_m = "invalid aggregator feed item from aggregator feed id (" . $feed_id . ") " .
                  "and md5 (" . $feed_item_md5 . ")";
            $_i = $user_profile_id;                          
            $_d = array('method' => __METHOD__, 'user_profile_id' => $_i);
            throw new B_Exception($_m, E_USER_WARNING, $_d);
        }

        $feed_item_id = $feed_item->aggregator_feed_item_id;

        /* new queue item */

        $queue_item = new QueueItem();
        $queue_item->aggregator_feed_item_id = $feed_item_id;
        $queue_item->user_blog_id = $blog_id;
        $queue_item->populate($feed_item->dump());
        $queue_item->save();

        return $queue_item;
    }
}
