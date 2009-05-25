<?php

/**
 * UserBlogFeed model class
 * 
 * @category    PostCanal
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
     * Get UserBlogFeed by primary key
     *
     * @param   integer $id    Primary key value
     *
     * @return  UserBlogFeed|null 
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
                   "SET ordering=(ordering + 1) " .
                   "WHERE user_blog_id = ?";
            self::execute($sql, array($this->user_blog_id));
        }
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
        return current(self::select(
            "SELECT * FROM " . self::$table_name . 
            " WHERE user_blog_id = ? AND aggregator_feed_id = ?", 
            array($blog_id, $feed_id), PDO::FETCH_CLASS, get_class()));
    }

    /**
     * Get by Blog and Feed hash
     *
     * @param   integer $user_id
     * @param   string  $blog_hash
     * @param   string  $feed_hash
     * @return  UserBlog|null
     */
    public static function getByBlogAndFeedHash($user_id, $blog_hash, $feed_hash)
    {
        $_s = "SELECT * FROM " . self::$table_name . "
               WHERE hash = ? AND user_blog_id = (
                    SELECT user_blog_id FROM model_user_blog
                    WHERE user_profile_id = ? AND hash = ?)";
        $_d = array($feed_hash, $user_id, $blog_hash);
        return current(self::select($_s, $_d, PDO::FETCH_CLASS, get_class()));
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
        return current(self::select(
            "SELECT * FROM " . self::$table_name . 
            " WHERE user_blog_id = ? AND hash = ?", 
            array($blog_id, $hash), PDO::FETCH_CLASS, get_class()));
    }

    /**
     * Find by User Blog
     *
     * @param   string      $blog_hash
     * @param   integer     $user_id
     * @param   boolean     $enabled        Show enabled only
     *
     * @return  array
     */
    public static function findAssocByBlogAndUser($blog_hash, $user_id, $enabled=true)
    {
        $sql = "SELECT a.hash as feed, b.feed_url, a.feed_title,
                       a.feed_description, b.feed_update_time,
                       b.feed_status, a.ordering, a.enabled
                FROM " . self::$table_name . " AS a
                LEFT JOIN model_aggregator_feed AS b
                ON (a.aggregator_feed_id = b.aggregator_feed_id)
                WHERE a.user_blog_id = (
                    SELECT user_blog_id
                    FROM model_user_blog
                    WHERE hash = ? AND user_profile_id = ?) ";
        if($enabled==true)
        {
            $sql.= "AND a.enabled = 1 ";
        }
        $sql.= "AND b.enabled = 1 AND deleted = 0
                ORDER BY a.ordering ASC, a.created_at DESC";

        return self::select($sql, array($blog_hash, $user_id), PDO::FETCH_ASSOC);
    }

    /**
     * Find feed articles for a user blog feed
     *
     * @param   string      $blog_hash
     * @param   integer     $user_id
     * @param   integer     $feed_hash
     * @param   integer     $older
     * @param   integer     $limit
     *
     * @return  array
     */
    public static function findArticlesThreaded($blog_hash, 
                                                $user_id, 
                                                $feed_hash,
                                                $older=null, 
                                                $limit=25)
    {
        if(!$older) $older = time();

        $sql = "SELECT article_md5 AS article, article_title AS title, article_link AS link, 
                       article_date AS date, article_author AS author, article_content AS content
                FROM model_user_blog_feed AS a
                LEFT JOIN model_aggregator_feed_article AS b
                ON (a.aggregator_feed_id = b.aggregator_feed_id)
                WHERE a.enabled = 1 AND a.deleted = 0 AND b.article_date < ? 
                AND a.hash = ? AND a.user_blog_id = (
                    SELECT user_blog_id
                    FROM model_user_blog
                    WHERE hash = ? AND user_profile_id = ?) 
                ORDER BY b.article_date DESC, b.created_at DESC LIMIT " . intval($limit);

        return self::select($sql, array(date("Y-m-d H:i:s", $older), 
                                        $feed_hash,
                                        $blog_hash, 
                                        $user_id), PDO::FETCH_ASSOC);
    }

    /**
     * Find feed articles associated with user blog feeds
     *
     * @param   string      $blog_hash
     * @param   integer     $user_id
     * @param   integer     $older
     * @param   integer     $limit
     *
     * @return  array
     */
    public static function findArticlesAll($blog_hash, 
                                           $user_id, 
                                           $older=null, 
                                           $limit=50)
    {
        if(!$older) $older= time();

        $sql = "SELECT a.feed_title AS feed, b.article_md5 AS article, 
                       b.article_title AS title, b.article_link AS link, 
                       b.article_date AS date, b.article_author AS author, 
                       b.article_content AS content
                FROM model_user_blog_feed AS a 
                LEFT JOIN model_aggregator_feed_article AS b 
                ON (a.aggregator_feed_id = b.aggregator_feed_id) 
                WHERE a.enabled = 1 AND a.deleted = 0 
                AND b.article_date < ? AND a.user_blog_id = (
                    SELECT user_blog_id
                    FROM model_user_blog
                    WHERE hash = ? AND user_profile_id = ?) 
                ORDER BY b.article_date DESC, b.created_at DESC LIMIT " . intval($limit);

        return self::select($sql, array(date("Y-m-d H:i:s", $older), 
                                        $blog_hash, 
                                        $user_id), PDO::FETCH_ASSOC);
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
        $_s = "SELECT * FROM model_user_blog_feed WHERE user_blog_id = (SELECT user_blog_id FROM model_user_blog WHERE hash = ? AND user_profile_id = ?) AND hash = ?";
        $_d = array($blog_hash, $user_id, $feed_hash);
        $_o = current(self::select($_s, $_d, PDO::FETCH_CLASS, get_class()));

        $i = $_o->ordering;
        $j = $ordering;
        $k = ($j < $i);

        if($j == $i) return null;

        $_s = "UPDATE model_user_blog_feed SET ";
        
        if($k)
        {
            $_s .= "ordering = (ordering + 1) ";
        }
        else
        {
            $_s .= "ordering = (ordering - 1) ";
        }
        
        $_s .= "WHERE user_blog_id = (SELECT user_blog_id FROM model_user_blog WHERE hash = ? AND user_profile_id = ?) ";
        $_d = array($blog_hash, $user_id);

        if($k)
        {
            $_s .= "AND ordering >= ? AND ordering < ?";
            $_d = array_merge($_d, array($j, $i));
        }
        else
        {
            $_s .= "AND ordering > ? AND ordering <= ?";
            $_d = array_merge($_d , array($i, $j));
        }

        self::execute($_s, $_d);

        $_o->ordering = $j;
        $_o->save();
    }
}
