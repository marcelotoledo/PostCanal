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
     * Find by user blog
     */ 
    public static function findByUserBlog($user_profile_id, $blog_hash)
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

        return self::find(array('user_blog_id' => $blog_id),
                          array('created_at DESC'));
    }

    /**
     * Get queue item that need publication
     * 
     * @return  array
     */
    public static function findNeedPublish()
    {
        $sql = "SELECT 
                    a.user_blog_queue_item_id as id, 
                    a.item_title AS item_title,
                    a.item_content AS item_content,
                    b.manager_url as manager_url,
                    b.manager_username as manager_username,
                    b.manager_password as manager_password,
                    c.type_name as blog_type,
                    c.version_name as blog_version 
                FROM 
                    model_user_blog_queue_item AS a 
                LEFT JOIN 
                    model_user_blog AS b ON (a.user_blog_id = b.user_blog_id) 
                LEFT JOIN 
                    model_blog_type AS c ON (b.blog_type_id = c.blog_type_id) 
                LIMIT 1"; /* < todo: order by ... publish flags ... */

        return array();
    }

    /**
     * Find Queue item from Hash
     *
     * @param   integer $blog_id
     * @param   string  $hash
     * @return  QueueItem|null
     */
    public static function findByHash($blog_id, $hash)
    {
        return current(self::find(array(
            'user_blog_id' => $blog_id,
            'hash' => $hash)));
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

    /**
     * Set queue item to publish
     *
     * @param   string  $item_hash
     * @param   string  $blog_hash
     * @param   integer $user_profile_id
     */ 
    public static function itemToPublish($item_hash, 
                                         $blog_hash,
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

        /* get queue item */

        if(!is_object(($item = self::findByHash($blog_id, $item_hash))))
        {
            $_m = "invalid queue item from hash (" . $tem_hash . ")";
            $_i = $user_profile_id;                          
            $_d = array('method' => __METHOD__, 'user_profile_id' => $_i);
            throw new B_Exception($_m, E_USER_WARNING, $_d);
        }

        $item->to_publish = true;
        $item->save();
    }

    /**
     * Check queue items status
     *
     * @param   array   $array_item_hash
     * @param   string  $blog_hash
     * @param   integer $user_profile_id
     */ 
    public static function checkToPublish($array_item_hash,
                                          $blog_hash,
                                          $user_profile_id)
    {
        $sql = "SELECT hash FROM " . self::$table_name . " " . 
               "WHERE user_blog_id = (" .
                    "SELECT user_blog_id FROM model_user_blog " .
                    "WHERE hash = ? and user_profile_id = ?) " .
               "AND to_publish = 1 AND published = 0";

        $to_publish = array();

        foreach(self::select($sql, array($blog_hash, $user_profile_id), PDO::FETCH_ASSOC) as $i)
        {
            $to_publish[] = $i['hash'];
        }

        return array_diff($array_item_hash, $to_publish);
    }
}
