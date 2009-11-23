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
		'visible' => array ('type' => 'boolean','size' => 0,'required' => false),
		'deleted' => array ('type' => 'boolean','size' => 0,'required' => false));

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

    public static $allow_write = array 
    (
        'feed_title','feed_description'
    );

    const ARTICLES_MAX = 50;
    

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
            $this->setDefaultOrdering();
        }

        return parent::save();
    }

    /**
     * Set default ordering
     */
    public function setDefaultOrdering()
    {
        $this->ordering = 1;
        $this->increaseOrdering();
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
                   "WHERE user_blog_id = ? AND ordering > 0";
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
     *
     * @return  array
     */
    public static function findAssocByBlogAndUser($blog_hash, $user_id)
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
                AND a.visible = 1 AND a.deleted = 0
                ORDER BY a.ordering ASC, a.created_at DESC";

        $feeds = self::select($sql, array($blog_hash, $user_id), PDO::FETCH_ASSOC);
        $total_feeds = count($feeds);
        $tags = UserBlogFeedTag::findCSVFeedTags($user_id, $blog_hash);

        for($j=0;$j<$total_feeds;$j++)
        {
            $feed_hash=$feeds[$j]['feed'];
            $feeds[$j]['tags'] = (array_key_exists($feed_hash, $tags)) ?
                $tags[$feed_hash] : '';
        }

        return $feeds;
    }

    /**
     * Find by User Blog (group by tags)
     */
    public static function findGroupByTags($blog_hash, $user_id)
    {
        $sql = "SELECT c.name AS tag, b.hash AS feed, d.feed_url, b.feed_title, 
                       b.feed_description, d.feed_update_time, 
                       d.feed_status, b.ordering
                FROM model_user_blog_feed_tag AS a 
                LEFT JOIN model_user_blog_feed AS b 
                    ON (a.user_blog_feed_id = b.user_blog_feed_id) 
                LEFT JOIN model_user_blog_tag AS c 
                    ON (a.user_blog_tag_id = c.user_blog_tag_id) 
                LEFT JOIN model_aggregator_feed AS d 
                    ON (b.aggregator_feed_id = d.aggregator_feed_id)
                WHERE b.user_blog_id = (
                    SELECT user_blog_id
                    FROM model_user_blog
                    WHERE hash = ? AND user_profile_id = ?)
                AND b.visible = 1 AND b.deleted = 0
                ORDER BY b.ordering ASC, b.created_at DESC";

        $feeds = self::select($sql, array($blog_hash, $user_id), PDO::FETCH_ASSOC);
        $total_feeds = count($feeds);
        $tag_feeds = array();

        for($j=0;$j<$total_feeds;$j++)
        {
            $tag = $feeds[$j]['tag'];
            if(array_key_exists($tag, $tag_feeds)==false) $tag_feeds[$tag] = array();
            $tag_feeds[$tag][] = $feeds[$j];
        }

        $outarr = array();

        foreach($tag_feeds as $tag => $feeds)
        {
            $outarr[] = array('tag_name' => $tag, 'tag_feeds' => $feeds);
        }

        return $outarr;
    }

    // /**
    //  * Find total unread articles (TODO)
    //  */
    // public static function findTotalUnreadByBlogAndUser($blog_hash, $user_id)
    // {
    //     $sql = "SELECT COUNT(a.aggregator_feed_article_id) AS wr, 
    //                 b.aggregator_feed_id AS feed_id 
    //             FROM model_user_blog_feed_article AS a 
    //             LEFT JOIN model_aggregator_feed_article AS b 
    //                 ON (a.aggregator_feed_article_id = b.aggregator_feed_article_id) 
    //             LEFT JOIN model_user_blog AS c 
    //                 ON (a.user_blog_id = c.user_blog_id) 
    //             WHERE c.hash = ? AND c.user_profile_id = ?
    //             GROUP BY b.aggregator_feed_id";
    //     $wr = self::select($sql, array($blog_hash, $user_id), PDO::FETCH_ASSOC);
    // }

    /**
     * Find total by User Blog
     *
     * @param   string      $blog_hash
     * @param   integer     $user_id
     *
     * @return  array
     */
    public static function findTotalByBlogAndUser($blog_hash, $user_id)
    {
        $sql = "SELECT COUNT(*) AS total
                FROM " . self::$table_name . "
                WHERE user_blog_id = (
                    SELECT user_blog_id
                    FROM model_user_blog
                    WHERE hash = ? AND user_profile_id = ?)
                AND visible = 1 AND deleted = 0
                ORDER BY ordering ASC, created_at DESC";

        $r = current(self::select($sql, array($blog_hash, $user_id), PDO::FETCH_ASSOC));
        return $r['total'];
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
        $sql = "SELECT a.feed_title AS feed_title, a.hash AS feed, 
                       b.article_md5 AS article, b.article_title AS article_title, 
                       b.article_link AS article_link, b.created_at AS article_date, 
                       b.article_author AS article_author, b.article_content AS article_content,
                       c.publication_status AS publication_status,
                       c.hash AS entry,
                       d.was_read AS wr
                FROM model_user_blog_feed AS a
                LEFT JOIN model_aggregator_feed_article AS b
                    ON (a.aggregator_feed_id = b.aggregator_feed_id)
                LEFT JOIN model_user_blog_entry AS c
                    ON (b.aggregator_feed_article_id = c.aggregator_feed_article_id) 
                    AND (a.user_blog_id = c.user_blog_id)
                    AND (c.deleted = 0)
                LEFT JOIN model_user_blog_feed_article AS d
                    ON (a.user_blog_id = d.user_blog_id)
                    AND (b.aggregator_feed_article_id = d.aggregator_feed_article_id)
                WHERE a.visible = 1 AND a.deleted = 0 ";
        if($older) $sql.= "AND b.updated_at < ? ";
        $sql.= "AND a.hash = ? AND a.user_blog_id = (
                    SELECT user_blog_id
                    FROM model_user_blog
                    WHERE hash = ? AND user_profile_id = ?) 
                ORDER BY b.created_at DESC, b.article_date DESC, b.aggregator_feed_article_id DESC
                LIMIT " . intval($limit);

        $args = array();
        if($older) $args[] = date("Y-m-d H:i:s", $older);
        $args[] = $feed_hash;
        $args[] = $blog_hash;
        $args[] = $user_id;

        return self::select($sql, $args, PDO::FETCH_ASSOC);
    }

    /**
     * Find feed articles by tag
     */
    public static function findArticlesTag($blog_hash, 
                                           $user_id, 
                                           $tag,
                                           $older=null, 
                                           $limit=25)
    {
        $sql = "SELECT a.feed_title AS feed_title, a.hash AS feed, 
                       b.article_md5 AS article, b.article_title AS article_title, 
                       b.article_link AS article_link, b.created_at AS article_date, 
                       b.article_author AS article_author, b.article_content AS article_content,
                       c.publication_status AS publication_status,
                       c.hash AS entry,
                       d.was_read AS wr
                FROM model_user_blog_feed AS a
                LEFT JOIN model_aggregator_feed_article AS b
                    ON (a.aggregator_feed_id = b.aggregator_feed_id)
                LEFT JOIN model_user_blog_entry AS c
                    ON (b.aggregator_feed_article_id = c.aggregator_feed_article_id) 
                    AND (a.user_blog_id = c.user_blog_id)
                    AND (c.deleted = 0)
                LEFT JOIN model_user_blog_feed_article AS d
                    ON (a.user_blog_id = d.user_blog_id)
                    AND (b.aggregator_feed_article_id = d.aggregator_feed_article_id)
                INNER JOIN model_user_blog_tag AS e
                    ON (a.user_blog_id = e.user_blog_id)
                    AND (e.name = ?)
                INNER JOIN model_user_blog_feed_tag AS f
                    ON (a.user_blog_feed_id = f.user_blog_feed_id)
                    AND (e.user_blog_tag_id = f.user_blog_tag_id)
                WHERE a.visible = 1 AND a.deleted = 0 ";
        if($older) $sql.= "AND b.updated_at < ? ";
        $sql.= "AND a.user_blog_id = (
                    SELECT user_blog_id
                    FROM model_user_blog
                    WHERE hash = ? AND user_profile_id = ?) 
                ORDER BY b.created_at DESC, b.article_date DESC, b.aggregator_feed_article_id DESC
                LIMIT " . intval($limit);

        $args = array();
        $args[] = $tag;
        if($older) $args[] = date("Y-m-d H:i:s", $older);
        $args[] = $blog_hash;
        $args[] = $user_id;

        return self::select($sql, $args, PDO::FETCH_ASSOC);
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
                                           $limit=self::ARTICLES_MAX)
    {
        $sql = "SELECT a.feed_title AS feed_title, a.hash AS feed, 
                       b.article_md5 AS article, b.article_title AS article_title, 
                       b.article_link AS article_link, b.created_at AS article_date, 
                       b.article_author AS article_author, b.article_content AS article_content,
                       c.publication_status AS publication_status,
                       c.hash AS entry,
                       d.was_read AS wr
                FROM model_user_blog_feed AS a 
                LEFT JOIN model_aggregator_feed_article AS b 
                    ON (a.aggregator_feed_id = b.aggregator_feed_id) 
                LEFT JOIN model_user_blog_entry AS c
                    ON (b.aggregator_feed_article_id = c.aggregator_feed_article_id) 
                    AND (a.user_blog_id = c.user_blog_id)
                    AND (c.deleted = 0)
                LEFT JOIN model_user_blog_feed_article AS d
                    ON (a.user_blog_id = d.user_blog_id)
                    AND (b.aggregator_feed_article_id = d.aggregator_feed_article_id)
                WHERE a.visible = 1 AND a.deleted = 0 ";
        if($older) $sql.= "AND b.updated_at < ? ";
        $sql.= "AND a.user_blog_id = (
                    SELECT user_blog_id
                    FROM model_user_blog
                    WHERE hash = ? AND user_profile_id = ?) 
                ORDER BY b.created_at DESC, b.article_date DESC, b.aggregator_feed_article_id DESC
                LIMIT " . intval($limit);

        $args = array();
        if($older) $args[] = date("Y-m-d H:i:s", $older);
        $args[] = $blog_hash;
        $args[] = $user_id;

        return self::select($sql, $args, PDO::FETCH_ASSOC);
    }

    /**
     * Find writings
     */
    public static function findWritings($blog_hash, 
                                        $user_id, 
                                        $older=null, 
                                        $limit=25)
    {
        $sql = "SELECT a.feed_title AS feed_title, a.hash AS feed, 
                       b.article_md5 AS article, b.article_title AS article_title, 
                       b.article_link AS article_link, b.created_at AS article_date, 
                       b.article_author AS article_author, b.article_content AS article_content,
                       c.publication_status AS publication_status,
                       c.hash AS entry,
                       0 AS wr
                FROM model_user_blog_feed AS a
                LEFT JOIN model_aggregator_feed_article AS b
                    ON (a.aggregator_feed_id = b.aggregator_feed_id)
                LEFT JOIN model_user_blog_entry AS c
                    ON (b.aggregator_feed_article_id = c.aggregator_feed_article_id) 
                    AND (a.user_blog_id = c.user_blog_id)
                    AND (c.deleted = 0)
                WHERE a.visible = 0 AND a.deleted = 0 ";
        if($older) $sql.= "AND b.updated_at < ? ";
        $sql.= "AND a.user_blog_id = (
                    SELECT user_blog_id
                    FROM model_user_blog
                    WHERE hash = ? AND user_profile_id = ?)
                AND a.aggregator_feed_id = (
                    SELECT aggregator_feed_id
                    FROM model_aggregator_feed
                    WHERE feed_url_md5 = ?)
                ORDER BY b.created_at DESC, b.article_date DESC, b.aggregator_feed_article_id DESC
                LIMIT " . intval($limit);

        $args = array();
        if($older) $args[] = date("Y-m-d H:i:s", $older);
        $args[] = $blog_hash;
        $args[] = $user_id;
        $args[] = md5(sprintf(AggregatorFeed::WRITINGS_URL_BASE, $user_id, $blog_hash));

        return self::select($sql, $args, PDO::FETCH_ASSOC);
    }

    // /**
    //  * Find feed articles To Suggestion
    //  *
    //  * @param   string      $blog_id
    //  * @param   integer     $limit
    //  *
    //  * @return  array
    //  */
    // public static function findArticlesToSuggestion($blog_id, $limit=self::ARTICLES_MAX)
    // {
    //     $sql = "SELECT d.aggregator_feed_article_id AS article_id,
    //                    d.keywords AS keywords,
    //                    x.feed_ordering AS feed_ordering
    //             FROM (

    //                 SELECT a.aggregator_feed_article_id AS article_id, 
    //                        c.user_blog_entry_id AS entry_id,
    //                        b.ordering AS feed_ordering
    //                 FROM model_aggregator_feed_article AS a

    //                 LEFT JOIN model_user_blog_feed AS b 
    //                     ON (a.aggregator_feed_id = b.aggregator_feed_id)

    //                 LEFT JOIN model_user_blog_entry AS c
    //                     ON (a.aggregator_feed_article_id = c.aggregator_feed_article_id) 
    //                     AND (b.user_blog_id = c.user_blog_id)

    //                 WHERE b.enabled=1 AND b.deleted=0 AND b.user_blog_id = ?

    //                 ORDER BY a.created_at DESC, 
    //                          a.article_date DESC, 
    //                          a.aggregator_feed_article_id DESC

    //                 LIMIT " . intval($limit) . "

    //             ) AS x

    //             LEFT JOIN model_aggregator_feed_article AS d
    //                 ON (x.article_id = d.aggregator_feed_article_id)
    //             
    //             WHERE x.entry_id IS NULL";

    //     return self::select($sql, array($blog_id), PDO::FETCH_OBJ);
    // }

    /**
     * Update column
     * 
     * @param   integer     $user_id        
     * @param   string      $blog_hash
     * @param   string      $feed_hash
     * @param   string      $column_name
     * @param   string      $column_value
     * 
     * @return  string      feed_hash
     */
    public static function updateColumn($user, $blog, $feed, $name, $value)
    {
        $result = "";

        if(is_object(($_o = self::getByBlogAndFeedHash($user, $blog, $feed))))
        {
            $_o->{$name} = $value;
            $_o->save();
            $result = $feed;
        }

        return $result;
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
        $i = 1;

        self::transaction();

        foreach(self::findAssocByBlogAndUser($blog_hash, $user_id, false) as $f)
        {
            try
            {
                if($f['feed']==$feed_hash)
                {
                    self::updateColumn($user_id, $blog_hash, $feed_hash, 'ordering', $ordering);
                }
                else
                {
                    if($i==$ordering) { $i++; }
                    self::updateColumn($user_id, $blog_hash, $f['feed'], 'ordering', $i);
                    $i++;
                }
            }
            catch(Exception $e)
            {
                self::rollback();
                $m = "user blog feed ordering update failed for blog (" . $blog_hash . ") " .
                     ", feed (" . $f['feed'] . ") and ordering (" . $i . ");\n" . 
                     $e->getMessage();
                B_Log::write($m, E_ERROR);
            }
        }

        self::commit();
    }

    /**
     * Select total registered feeds
     */
    public static function total($user_id)
    {
        $q = current(self::select("SELECT COUNT(DISTINCT(aggregator_feed_id)) AS total
                                   FROM " . self::$table_name . 
                                 " WHERE deleted=0
                                   AND user_blog_id IN (
                                        SELECT user_blog_id
                                        FROM model_user_blog
                                        WHERE user_profile_id=?
                                        AND deleted=0
                                   )",
        array($user_id), PDO::FETCH_OBJ));

        return is_object($q) ? $q->total : 0;
    }
}
