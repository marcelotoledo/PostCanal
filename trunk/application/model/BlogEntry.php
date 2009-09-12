<?php

/**
 * BlogEntry model class
 * 
 * @category    PostCanal
 * @package     Application Model
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class BlogEntry extends B_Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected static $table_name = 'model_user_blog_entry';

    /**
     * Table structure
     *
     * @var array
     */
    protected static $table_structure = array (
		'user_blog_entry_id' => array ('type' => 'integer','size' => 0,'required' => false),
		'aggregator_feed_article_id' => array ('type' => 'integer','size' => 0,'required' => true),
		'user_blog_id' => array ('type' => 'integer','size' => 0,'required' => true),
		'hash' => array ('type' => 'string','size' => 8,'required' => true),
		'entry_title' => array ('type' => 'string','size' => 0,'required' => true),
		'entry_content' => array ('type' => 'string','size' => 0,'required' => true),
		'keywords' => array ('type' => 'string','size' => 0,'required' => false),
		'publication_status' => array ('type' => 'string','size' => 0,'required' => false),
		'publication_date' => array ('type' => 'date','size' => 0,'required' => false),
		'ordering' => array ('type' => 'integer','size' => 0,'required' => false),
		'created_at' => array ('type' => 'date','size' => 0,'required' => false),
		'updated_at' => array ('type' => 'date','size' => 0,'required' => false),
		'suggested' => array ('type' => 'boolean','size' => 0,'required' => false),
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
    protected static $primary_key_name = 'user_blog_entry_id';


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
     * Get BlogEntry by primary key
     *
     * @param   integer $id    Primary key value
     *
     * @return  BlogEntry|null 
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
     * publication_status column options 
     * 
     * The ordering here is relevant
     */
    const STATUS_IDLE         = 'idle';
    const STATUS_WAITING      = 'waiting';
    const STATUS_WORKING      = 'working';
    const STATUS_PUBLISHED    = 'published';
    const STATUS_UNAUTHORIZED = 'unauthorized';
    const STATUS_OVERQUOTA    = 'overquota';
    const STATUS_UNREACHABLE  = 'unreachable';
    const STATUS_FAILED       = 'failed';

    const ENQUEUEING_AUTO_MAX_ENTRIES = 10;


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

        /* generate keywords */

        $keywords = $this->entry_title . " " . $this->entry_content;
        L_Utility::keywords($keywords);
        $this->keywords = $keywords;

        return parent::save();
    }

    /**
     * Set default ordering for an entry
     */
    public function setDefaultOrdering()
    {
        $this->ordering = 1;

        if($this->user_blog_id)
        {
            $sql = "SELECT MAX(ordering) as maxord 
                    FROM " . self::$table_name . "
                    WHERE user_blog_id = ? 
                    AND publication_status != ?";

            $result = current(self::select($sql, array($this->user_blog_id, self::STATUS_PUBLISHED), PDO::FETCH_ASSOC));

            if($result['maxord'] > 0)
            {
                $this->ordering = $result['maxord'] + 1;
            }
        }
    }

    /**
     * Find entries in queue (not published)
     *
     * @return array
     */
    public static function findQueue($profile_id, $blog_hash)
    {
        $sql = "SELECT * FROM " . self::$table_name . "
                WHERE user_blog_id = (
                    SELECT user_blog_id FROM model_user_blog
                    WHERE hash = ? AND user_profile_id = ?) AND deleted=0
                AND publication_status != ? 
                ORDER BY ordering ASC ";

        return self::select($sql, array($blog_hash, $profile_id, self::STATUS_PUBLISHED), PDO::FETCH_CLASS, get_class());
    }

    /**
     * find las published entries
     *
     * @return array
     */
    public static function findPublished($profile_id, $blog_hash, $last=10)
    {
        $sql = "SELECT * FROM " . self::$table_name . "
                WHERE user_blog_id = (
                    SELECT user_blog_id FROM model_user_blog
                    WHERE hash = ? AND user_profile_id = ?) 
                AND publication_status = ? AND deleted=0
                ORDER BY publication_date DESC LIMIT " . intval($last);

        return self::select($sql, array($blog_hash, $profile_id, self::STATUS_PUBLISHED),  PDO::FETCH_CLASS, get_class());
    }

    /**
     * Get keywords from last published entries
     *
     * return array
     */
    public static function findLastPublishedKeywords($blog_id, $last=10)
    {
        $sql = "SELECT keywords FROM " . self::$table_name . "
                WHERE user_blog_id = ?
                AND publication_status = ? AND deleted=0
                ORDER BY publication_date DESC LIMIT " . intval($last);

        return self::select($sql, array($blog_id, self::STATUS_PUBLISHED), PDO::FETCH_OBJ);
    }

    /**
     * Get max publication date (top future) from queue entries
     *
     * return integer
     */
    public static function getMaxPublicationTime($blog_id)
    {
        $sql = "SELECT MAX(publication_date) as maxpub
                FROM " . self::$table_name . "
                WHERE user_blog_id = ? AND publication_status != ? AND deleted=0"; 
        $result = current(self::select($sql, array($blog_id, self::STATUS_PUBLISHED), PDO::FETCH_ASSOC));
        return intval($result['maxpub'])>0 ? strtotime($result['maxpub']) : time();
    }

    /**
     * Get blog entries that need publication
     * 
     * @return  array
     */
    public static function findAwaitingPublication($limit=1)
    {
        $sql = "SELECT 
                    a.user_blog_entry_id AS id, 
                    d.article_link       AS article_link,
                    a.entry_title        AS entry_title,
                    a.entry_content      AS entry_content,
                    b.blog_manager_url   AS blog_manager_url,
                    b.blog_username      AS blog_username,
                    b.blog_password      AS blog_password,
                    c.type_name          AS blog_type,
                    c.version_name       AS blog_version 
                FROM 
                    model_user_blog_entry AS a 
                LEFT JOIN 
                    model_user_blog AS b ON (a.user_blog_id = b.user_blog_id) 
                LEFT JOIN 
                    model_blog_type AS c ON (b.blog_type_id = c.blog_type_id) 
                LEFT JOIN
                    model_aggregator_feed_article AS d ON (a.aggregator_feed_article_id = d.aggregator_feed_article_id)
                WHERE
                    (       a.publication_status = ? 
                        OR (a.publication_status = ? AND (UNIX_TIMESTAMP(a.updated_at) + ?) < UNIX_TIMESTAMP())
                    )
                    AND a.publication_date < NOW() 
                    AND a.deleted = 0
                ORDER BY
                    a.ordering ASC
                LIMIT " . intval($limit);

        self::transaction();

        if(($res = self::select($sql, array(self::STATUS_WAITING, self::STATUS_WORKING, intval(B_Registry::get('application/queue/entryWorkingTimeout'))), PDO::FETCH_ASSOC)))
        {
            /* working status avoid duplicated items on backend */

            $sql = "UPDATE model_user_blog_entry
                    SET publication_status='" . self::STATUS_WORKING . "', 
                        updated_at=NOW()
                    WHERE user_blog_entry_id=?";

            for($i=0;$i<count($res);$i++)
            {
                self::execute($sql, array($res[$i]['id']));
            }
        }

        self::commit();

        return $res;
    }

    /**
     * Get total blog entries that need publication
     * 
     * @return  integer
     */
    public static function getAwaitingPublicationTotal()
    {
        $sql = "SELECT COUNT(a.user_blog_entry_id) AS total
                FROM 
                    model_user_blog_entry AS a 
                LEFT JOIN 
                    model_user_blog AS b ON (a.user_blog_id = b.user_blog_id) 
                LEFT JOIN 
                    model_blog_type AS c ON (b.blog_type_id = c.blog_type_id) 
                WHERE
                    (       a.publication_status = ? 
                        OR (a.publication_status = ? AND (UNIX_TIMESTAMP(a.updated_at) + ?) < UNIX_TIMESTAMP())
                    )
                    AND a.publication_date < NOW() 
                    AND a.deleted = 0";

        $total = 0;

        if(($res = current(self::select($sql, array(self::STATUS_WAITING, self::STATUS_WORKING, intval(B_Registry::get('application/queue/entryWorkingTimeout'))), PDO::FETCH_ASSOC))))
        {
            $total = $res['total'];
        }

        return $total;
    }

    /** 
     * Release entry publication status working to waiting
     */
    public static function releaseWorking()
    {
        self::execute("UPDATE " . self::$table_name . " SET publication_status='" . self::STATUS_WAITING . "', updated_at=NOW() WHERE publication_status='" . self::STATUS_WORKING . "'");
    }

    /**
     * Set blog entry publication status
     * 
     * @param   integer            $id             Entry ID
     * @param   boolean|string     $status         Publication status?
     */
    public static function setPublicationStatus($id, $status=true)
    {
        $entry = self::getByPrimaryKey($id);

        if(is_object($entry))
        {
            if(in_array($status, array
                (
                    self::STATUS_PUBLISHED,
                    self::STATUS_UNAUTHORIZED,
                    self::STATUS_UNREACHABLE,
                    self::STATUS_FAILED,
                )))
            {
                $entry->publication_status = $status;
            }
            else
            {
                $entry->publication_status = ((boolean) $status) ?
                    self::STATUS_PUBLISHED :
                    self::STATUS_FAILED;
            }

            $entry->save();
        }
    }

    /**
     * Find blog entry from Hash
     *
     * @param   integer $blog_id
     * @param   string  $hash
     * @return  BlogEntry|null
     */
    public static function getByBlogAndEntryHash($profile_id, $blog_hash, $entry_hash)
    {
        $sql = "SELECT * FROM " . self::$table_name . " WHERE hash = ? 
                AND user_blog_id = (
                    SELECT user_blog_id FROM model_user_blog 
                    WHERE user_profile_id = ? AND hash = ?)";
        $args = array($entry_hash, $profile_id, $blog_hash);

        return current(self::select($sql, $args, PDO::FETCH_CLASS, get_class()));
    }

    /**
     * Create entry from feed article Assoc result
     */
    protected static function newFromFeedArticleAssoc($result)
    {
        $entry = new self();

        if($result['publication_auto']==1)
        {
            /* new item start waiting publication */

            $entry->publication_status = self::STATUS_WAITING;
        }

        $mtime = self::getMaxPublicationTime($result['user_blog_id']);
        $mtime+= intval(B_Registry::get('application/queue/publicationMargin'));
        $mtime+= $result['publication_interval'];
        $entry->publication_date = $mtime;

        $entry->populate($result);
        $entry->save();

        return array(
            'entry'              => $entry->hash,
            'entry_title'        => $entry->entry_title,
            'entry_content'      => $entry->entry_content,
            'publication_status' => $entry->publication_status,
            'publication_date'   => $entry->publication_date
        );
    }

    /**
     * Create entry to blog from feed article
     *
     * @param   string  $feed_article_md5
     * @param   string  $blog_hash
     * @param   string  $feed_hash
     * @param   string  $profile_id
     *
     * @return  BlogEntry
     */ 
    public static function newFromFeedArticle($article_md5, 
                                              $blog_hash, 
                                              $feed_hash, 
                                              $profile_id)
    {
        $sql = "SELECT a.aggregator_feed_article_id AS aggregator_feed_article_id,
                       a.article_title AS entry_title,
                       a.article_content AS entry_content,
                       b.user_blog_id AS user_blog_id,
                       c.publication_auto as publication_auto,
                       c.publication_interval as publication_interval
                FROM model_aggregator_feed_article AS a
                LEFT JOIN model_user_blog_feed AS b ON (a.aggregator_feed_id = b.aggregator_feed_id)
                LEFT JOIN model_user_blog AS c ON (b.user_blog_id = c.user_blog_id)
                WHERE a.article_md5 = ? AND b.hash = ? 
                AND c.user_profile_id = ? AND c.hash = ?";
        $args = array($article_md5, $feed_hash, $profile_id, $blog_hash);
        $result = current(self::select($sql, $args, PDO::FETCH_ASSOC));

        return self::newFromFeedArticleAssoc($result);
    }

    /**
     * Create entry to blog from feed article and Blog ID
     */
    public static function newFromArticleBlogId($article_id, $blog_id)
    {
        $result = array();

        if(is_object($article = AggregatorFeedArticle::getByPrimaryKey($article_id)) &&
           is_object($blog = UserBlog::getByPrimaryKey($blog_id)))
        {
            $result = array
            (
                'aggregator_feed_article_id' => $article->aggregator_feed_article_id,
                'entry_title'                => $article->article_title,
                'entry_content'              => $article->article_content,
                'user_blog_id'               => $blog->user_blog_id,
                'publication_auto'           => $blog->publication_auto,
                'publication_interval'       => $blog->publication_interval,
                'suggested'                  => true
            );
        }

        return self::newFromFeedArticleAssoc($result);
    }

    /**
     * update entry to publish
     *
     * @param   string  $entry_hash
     * @param   string  $blog_hash
     * @param   integer $user_profile_id
     * @param   integer $pts                Publication date (in timestamp)
     * 
     * @return  boolean
     */ 
    public static function updateEntryToPublish($entry_hash, $blog_hash,$profile_id, $pts=0)
    {
        $updated = false;

        if(is_object(($entry = self::getByBlogAndEntryHash($profile_id,
                                                           $blog_hash,
                                                           $entry_hash))))
        {
            $entry->publication_status = self::STATUS_WAITING;
            $entry->publication_date = $pts>0 ? $pts : time();
            $entry->save();
            $updated = true;
        }

        return $updated;
    }

    /**
     * Check blog entries publish status
     *
     * @param   array   $array_entry_hash
     * @param   string  $blog_hash
     * @param   integer $user_profile_id
     */ 
    public static function checkStatus($array_entry_hash,
                                       $blog_hash,
                                       $profile_id)
    {
        /* sanitize hash list */

        for($i=0;$i<count($array_entry_hash);$i++)
        {
            $array_entry_hash[$i] = preg_replace("/[^\w]+/", "", $array_entry_hash[$i]);
        }

        $_in = "'" . implode("','", $array_entry_hash) . "'";

        $_q = "SELECT hash AS entry, publication_status AS status 
               FROM " . self::$table_name . "
               WHERE user_blog_id = (
                    SELECT user_blog_id FROM model_user_blog
                    WHERE hash = ? and user_profile_id = ?)
               AND hash IN (" . $_in . ")";

        $result = array();

        return self::select($_q, array($blog_hash, $profile_id), PDO::FETCH_ASSOC);
    }

    /**
     * Update column
     * 
     * @param   integer     $user_id        
     * @param   string      $blog_hash
     * @param   string      $entry_hash
     * @param   string      $column_name
     * @param   string      $column_value
     * 
     * @return  string      entry_hash
     */
    public static function updateColumn($user, $blog, $entry, $name, $value)
    {
        $result = "";

        if(is_object(($_o = self::getByBlogAndEntryHash($user, $blog, $entry))))
        {
            $_o->{$name} = $value;
            $_o->save();
            $result = $entry;
        }

        return $entry;
    }

    /**
     * Update queue ordering
     *
     * @param   string      $blog_hash
     * @param   integer     $profile_id        
     * @param   string      $entry_hash
     * @param   integer     $ordering
     */
    public static function updateOrdering($blog_hash, $profile_id, $entry_hash, $ordering)
    {
        $j = 1;

        self::transaction();

        foreach(self::findQueue($profile_id, $blog_hash) as $o)
        {
            if($o->hash==$entry_hash)
            {
                $o->ordering = $ordering;
            }
            else
            {
                if($j==$ordering) { $j++; }
                $o->ordering = $j;
                $j++;
            }

            try
            {
                $o->save();
            }
            catch(Exception $e)
            {
                self::rollback();
                $m = "user blog entry ordering update failed for blog (" . $blog_hash . ") " .
                     ", entry (" . $o->hash . ") and ordering (" . $j . ");\n" . 
                     $e->getMessage();
                B_Log::write($m, E_ERROR);
            }
        }

        self::commit();

        /* update publication interval */

        $_b = UserBlog::getByUserAndHash($profile_id, $blog_hash);
        $_a = $_b->publication_auto;
        $_i = $_b->publication_interval;

        self::updateAutoPublication($blog_hash, $profile_id, $_a, $_i);
    }

    /**
     * Update entries acording to auto publication
     *
     * @param   string      $blog           Blog Hash
     * @param   integer     $user           User Profile ID
     * @param   boolean     $publication    Publication Auto?
     * @param   integer     $interval       Entry publication interval
     */
    public static function updateAutoPublication($blog, $user, $publication, $interval)
    {
        if($interval<=0)
        {
            $interval = intval(B_Registry::get('application/queue/publicationMargin'));
        }

        $t = time();

        /* save blog publication interval */

        $_b = UserBlog::getByUserAndHash($user, $blog);
        $_b->publication_interval = $interval;
        $_b->save();

        /* update entries */

        foreach($queue = self::findQueue($user, $blog) as $o)
        {
            $o->publication_status = ($publication==true) ? self::STATUS_WAITING :
                                                            self::STATUS_IDLE ;
            $o->publication_date = ($t+=$interval);
            $o->save();
        }
    }

    /**
     * Delete queue entry AND return Article HASH
     *
     * @param   string      $blog           Blog Hash
     * @param   string      $entry          Blog Entry
     * @param   integer     $user           User Profile ID
     * @return  string                      AggregatorFeedArticle Hash
     */
    public static function deleteEntry($user, $blog, $entry)
    {
        $sql = "SELECT a.*, b.article_md5 
                FROM " . self::$table_name . " AS a
                LEFT JOIN model_aggregator_feed_article AS b 
                    ON (a.aggregator_feed_article_id = b.aggregator_feed_article_id)
                WHERE a.hash = ? AND user_blog_id = (
                    SELECT user_blog_id FROM model_user_blog
                    WHERE user_profile_id = ? AND hash = ?)";
        $args = array($entry, $user, $blog);

        $article = null;

        if(is_object($obj = current(self::select($sql, $args, PDO::FETCH_CLASS, get_class()))))
        {
            $article = $obj->article_md5;
            $obj->deleted = 1;
            $obj->save();
        }

        return $article;
    }

    /**
     * Do queue entry suggestion when enqueueing_auto is true
     */
    public static function suggestEntry()
    {
        /* get blog */

        $max = intval(B_Registry::get('application/queue/suggestMaxEntries'));

        $sql = "SELECT 
                    a.user_blog_id AS blog_id, 
                    keywords AS keywords
                FROM model_user_blog AS a 
                LEFT JOIN (
                    SELECT user_blog_id, COUNT(user_blog_entry_id) AS entries
                    FROM model_user_blog_entry 
                    WHERE suggested=1 AND deleted=0 AND publication_status != ?
                    GROUP BY user_blog_id) AS x
                ON (a.user_blog_id = x.user_blog_id)
                WHERE enqueueing_auto=1 AND enabled=1
                AND (x.entries < " . $max . " OR x.entries IS NULL)
                ORDER BY enqueueing_auto_updated_at ASC LIMIT 1";

        $articles = array();
        $keywords = array();

        self::transaction();

        if(is_object($blog = current(self::select($sql, array(self::STATUS_PUBLISHED), PDO::FETCH_OBJ)))==false) 
        {
            self::rollback();
            return 0;
        }

        $sql = "UPDATE model_user_blog
                SET enqueueing_auto_updated_at = NOW()
                WHERE user_blog_id = ?";

        self::execute($sql, array($blog->blog_id));

        self::commit();

        /* get blog keywords */

        $separator = null;
        if    (strpos($blog->keywords, ",")>0) $separator = ",";
        elseif(strpos($blog->keywords, ":")>0) $separator = ":";
        elseif(strpos($blog->keywords, ";")>0) $separator = ";";
        elseif(strpos($blog->keywords, "|")>0) $separator = "|";

        $keywords = array();

        if($separator==null)
        {
            if(strpos($blog->keywords, " ")>0)
            {
                $k = $blog->keywords;
                L_Utility::keywords($k);
                $keywords = explode(" ", $k);
            }
        }
        else
        {
            foreach(explode($separator, $blog->keywords) AS $k)
            {
                L_Utility::keywords($k);

                if(strlen($k)>0)
                {
                    $keywords[] = $k;
                }
            }
        }

        /* get keywords based on last publications */

        if(count($keywords)==0)
        {
            $epub = self::findLastPublishedKeywords($blog->blog_id);
            $etot = count($epub);
            $fmin = round($etot * 0.2); // discard low freq keywords
            $buff = array();

            /* calculate keyword freq */

            foreach($epub as $e)
            {
                foreach(explode(" ", $e->keywords) as $k)
                {
                    array_key_exists($k, $buff) ? $buff[$k]++ : $buff[$k] = 1;
                }
            }

            /* get most popular keywords */

            foreach($buff as $k => $f)
            {
                if($f >= $fmin)
                {
                    $keywords[] = $k;
                }
            }
        }

        /* rank articles */

        $rank = array();

        $articles = UserBlogFeed::findArticlesToSuggestion($blog->blog_id);

        if(count($articles)>0)
        {
            /* suggestion based on keywords */
    
            if(count($keywords)>0)
            {
                $nk = count($keywords);
                $jk = 0;

                foreach($articles as $a)
                {
                    $a->keywords = " " . $a->keywords; // fix for strpos==0

                    for($jk=0;$jk<$nk;$jk++)
                    {
                        if(strpos($a->keywords, $keywords[$jk])>0)
                        {
                            array_key_exists($a->article_id, $rank) ?
                                $rank[$a->article_id]++ :
                                $rank[$a->article_id] = 1 ;
                        }
                    }

                    /* feed ordering ponderation */

                    if($a->feed_ordering > 0)
                    {
                        array_key_exists($a->article_id, $rank) ?
                            $rank[$a->article_id] *= (1 / $a->feed_ordering) :
                            $rank[$a->article_id] = 0 ;
                    }
                }
            }

            /* suggestion based on top article */
            
            else
            {
                foreach($articles as $a)
                {
                    if($a->feed_ordering > 0)
                    {
                        $rank[$a->article_id] = (1 / $a->feed_ordering);
                    }
                }
            }

        }

        /* get top ranked article */

        $ma = 0;
        $mr = 0;

        foreach($rank as $a=>$r)
        {
            if($r > $mr)
            {
                $ma = $a;
                $mr = $r;
            }
        }

        /* enqueue */

        if($ma>0)
        {
            self::newFromArticleBlogId($ma, $blog->blog_id);
            $_m = "suggestion of article (" . $ma . ") " . 
                  "added to blog (" . $blog->blog_id . ")";
            B_Log::write($_m, E_NOTICE);
        }

        return $blog->blog_id;
    }

    /**
     * Find total publication in a period
     */
    public static function totalPeriod($profile_id, $period)
    {
        $sql = "SELECT COUNT(*) AS total 
                FROM " . self::$table_name . " 
                WHERE publication_status=?
                AND publication_date > (UNIX_TIMESTAMP() - ?)
                AND user_blog_id IN (
                    SELECT user_blog_id
                    FROM model_user_blog
                    WHERE user_profile_id=?
                    AND deleted=0
                )";
       // TODO...
    }
}
