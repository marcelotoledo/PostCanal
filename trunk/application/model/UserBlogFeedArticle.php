<?php

/**
 * UserBlogFeedArticle model class
 * 
 * @category    PostCanal
 * @package     Application Model
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class UserBlogFeedArticle extends B_Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected static $table_name = 'model_user_blog_feed_article';

    /**
     * Table structure
     *
     * @var array
     */
    protected static $table_structure = array (
		'user_blog_feed_id' => array ('type' => 'integer','size' => 0,'required' => true),
		'aggregator_feed_article_id' => array ('type' => 'integer','size' => 0,'required' => true),
		'was_read' => array ('type' => 'boolean','size' => 0,'required' => false));

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
    protected static $primary_key_name = null;


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
     * Get UserBlogFeedArticle by primary key
     *
     * @param   integer $id    Primary key value
     *
     * @return  UserBlogFeedArticle|null 
     */
    public static function getByPrimaryKey($id)
    {
        return null;
    }

    // -------------------------------------------------------------------------

    public static function setArticleReadAttr($user, $blog, $feed, $article, $wr=true)
    {
        $blog = UserBlog::getByUserAndHash($user, $blog);
        $feed = UserBlogFeed::getByBlogAndHash($blog->user_blog_id, $feed);
        return self::execute('REPLACE INTO ' . self::$table_name . ' (user_blog_feed_id, aggregator_feed_article_id, was_read) VALUES (?, (SELECT aggregator_feed_article_id FROM model_aggregator_feed_article WHERE article_md5 = ? LIMIT 1), ?)', array($feed->user_blog_feed_id, $article, ($wr ? 1 : 0)));
    }
}
