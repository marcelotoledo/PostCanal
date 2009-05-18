<?php

/**
 * BlogEntry model class
 * 
 * @category    Blotomate
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
		'status_' => array ('type' => 'string','size' => 0,'required' => false),
		'publication_date' => array ('type' => 'date','size' => 0,'required' => false),
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
        return self::get(array(self::$primary_key_name => $id));
    }

    // -------------------------------------------------------------------------

    /**
     * status_ column options 
     * 
     * The ordering here is relevant
     */
    const STATUS_NEW       = 'new';
    const STATUS_WAITING   = 'publication_waiting';
    const STATUS_FAILED    = 'publication_failed';
    const STATUS_PUBLISHED = 'published';

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
        }

        return parent::save();
    }

    /**
     * Find by user blog
     */ 
    public static function findByUserAndBlogHash($profile_id, $blog_hash)
    {
        $_in = "'" . implode("','", array(self::STATUS_NEW, 
                                          self::STATUS_WAITING, 
                                          self::STATUS_FAILED)) . "'";

        $sql = "SELECT * FROM " . self::$table_name . "
                WHERE user_blog_id = (
                    SELECT user_blog_id FROM model_user_blog
                    WHERE hash = ? AND user_profile_id = ?)
                AND status_ IN (" . $_in . ") AND deleted = 0
                ORDER BY publication_date ASC, updated_at ASC";
               
        return self::select($sql, array($blog_hash, $profile_id));
    }

    /**
     * Get blog entry that need publication
     * 
     * @return  array
     */
    public static function awaitingPublication()
    {
        $sql = "SELECT 
                    a.user_blog_entry_id AS id, 
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
                WHERE
                    a.status_ = ? AND
                    a.publication_date < UTC_TIMESTAMP() AND
                    a.deleted = 0
                ORDER BY
                    a.updated_at ASC
                LIMIT 1";

        return current(self::select($sql, array(self::STATUS_WAITING), PDO::FETCH_ASSOC));
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
     * Create entry to blog from feed article
     *
     * @param   string  $feed_article_md5
     * @param   string  $blog_hash
     * @param   string  $feed_hash
     * @param   string  $profile_id
     *
     * @return  BlogEntry
     */ 
    public static function newFromFeedArticle($feed_article_md5, 
                                              $blog_hash, 
                                              $feed_hash, 
                                              $profile_id)
    {
        $sql = "SELECT a.aggregator_feed_article_id AS aggregator_feed_article_id,
                       a.article_title AS entry_title,
                       a.article_content AS entry_content,
                       b.user_blog_id AS user_blog_id
                FROM model_aggregator_feed_article AS a
                LEFT JOIN model_user_blog_feed AS b ON (a.aggregator_feed_id = b.aggregator_feed_id)
                LEFT JOIN model_user_blog AS c ON (b.user_blog_id = c.user_blog_id)
                WHERE a.article_md5 = ? AND b.hash = ? 
                AND c.user_profile_id = ? AND c.hash = ?";
        $args = array($feed_article_md5, $feed_hash, $profile_id, $blog_hash);

        $result = current(self::select($sql, $args, PDO::FETCH_ASSOC));

        /* new entry */

        $blog_entry = new self();
        $blog_entry->populate($result);
        $blog_entry->save();

        return $blog_entry;
    }

    /**
     * update entry to publish
     *
     * @param   string  $entry_hash
     * @param   string  $blog_hash
     * @param   integer $user_profile_id
     */ 
    public static function updateEntryToPublish($entry_hash, 
                                                $blog_hash,
                                                $user_profile_id)
    {
        if(is_object(($entry = self::getByBlogAndEntryHash($user_profile_id,
                                                           $blog_hash,
                                                           $entry_hash))))
        {
            $entry->status_ = self::STATUS_WAITING;
            $entry->publication_date = time(); // asap
            $entry->save();
        }
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

        $_q = "SELECT hash AS entry, status_ AS status 
               FROM " . self::$table_name . "
               WHERE user_blog_id = (
                    SELECT user_blog_id FROM model_user_blog
                    WHERE hash = ? and user_profile_id = ?)
               AND hash IN (" . $_in . ")";

        $result = array();

        return self::select($_q, array($blog_hash, $profile_id), PDO::FETCH_ASSOC);
    }

}
