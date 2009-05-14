<?php

/**
 * AggregatorFeedArticle model class
 * 
 * @category    Blotomate
 * @package     Application Model
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class AggregatorFeedArticle extends B_Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected static $table_name = 'model_aggregator_feed_article';

    /**
     * Table structure
     *
     * @var array
     */
    protected static $table_structure = array (
		'aggregator_feed_article_id' => array ('type' => 'integer','size' => 0,'required' => false),
		'aggregator_feed_id' => array ('type' => 'integer','size' => 0,'required' => true),
		'article_md5' => array ('type' => 'string','size' => 32,'required' => true),
		'article_date' => array ('type' => 'date','size' => 0,'required' => false),
		'article_link' => array ('type' => 'string','size' => 0,'required' => true),
		'article_title' => array ('type' => 'string','size' => 0,'required' => true),
		'article_author' => array ('type' => 'string','size' => 200,'required' => true),
		'article_content' => array ('type' => 'string','size' => 0,'required' => true),
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
    protected static $primary_key_name = 'aggregator_feed_article_id';


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
     * Get AggregatorFeedArticle by primary key
     *
     * @param   integer $id    Primary key value
     *
     * @return  AggregatorFeedArticle|null 
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
        /* generate article md5 */

        if($this->isNew()) 
        {
            if(strlen($this->article_link) > 0)
            {
                $this->article_md5 = md5($this->article_link);
            }
            else
            {
                $this->article_md5 = md5(A_Utility::randomString(8));
            }
        }

        return parent::save();
    }

    /**
     * Get AggregatorFeedArticle by article md5
     *
     * @param   integer $feed_id        AggregatorFeed ID
     * @param   string  $article_md5
     *
     * @return  AggregatorFeedArticle|null 
     */
    public static function getByArticleMd5($feed_id, $article_md5)
    {
        return current(self::select(
            "SELECT * FROM " . self::$table_name . 
            " WHERE aggregator_feed_id = ? AND article_md5 = ?",
            array($feed_id, $article_md5), PDO::FETCH_CLASS, get_class()));
    }

    /**
     * Find AggregatorFeedArticle by Feed
     *
     * @param   integer $feed           AggregatorFeed ID
     * @param   integer $time_offset
     * @param   integer $limit
     *
     * @return  array 
     */
    public static function findByFeed($feed, $time_offset=0, $limit=25)
    {
        $sql = "SELECT * FROM " . self::$table_name . "WHERE aggregator_feed_id = ? ";
        $args = array($feed_id);

        if($time_offset > 0)
        {
            $sql.= "AND article_date < ? ";
            $args[] = date('Y-m-d H:i:s', $time_offset);
        }

        $sql.= "ORDER BY article_date DESC LIMIT " . intval($limit);

        return self::select($sql, $args);
    }

    /**
     * Find AggregatorFeedArticle by Feed
     *
     * @param   integer $feed           AggregatorFeed ID
     * @param   integer $time_offset
     * @param   integer $limit
     *
     * @return  array 
     */
    public static function findAssocByFeed($feed_id, $time_offset=0, $limit=25)
    {
        $sql = "SELECT article_md5 AS article, 
                       article_date AS date, 
                       article_link AS link, 
                       article_title AS title, 
                       article_author as author, 
                       article_content AS content 
                FROM " . self::$table_name . " 
                WHERE aggregator_feed_id = ? ";

        $args = array($feed_id);

        if($time_offset > 0)
        {
            $sql.= "AND article_date < ? ";
            $args[] = date('Y-m-d H:i:s', $time_offset);
        }

        $sql.= "ORDER BY article_date DESC LIMIT " . intval($limit);

        return self::select($sql, $args, PDO::FETCH_ASSOC);
    }

    /**
     * Get last item time
     *
     * @param   integer $feed_id    AggregatorFeed ID
     *
     * @return  integer
     */
    public static function getLastItemTime($feed_id)
    {
        $sql = "SELECT UNIX_TIMESTAMP(item_date) AS last_item_time " .
               "FROM " . self::$table_name . " " .
               "WHERE aggregator_feed_id = ? " .
               "ORDER BY item_date DESC, created_at DESC LIMIT 1";

        $result = current(self::select($sql, array($feed_id)));

        return is_object($result) ? $result->last_item_time : 0;
    }

    /**
     * Insert feed item (raw)
     *
     * @param   AggregatorFeed  $feed
     * @param   array           $data
     * @return  integer
     */
    public static function rawInsert($feed, $data)
    {
        self::transaction();

        $feed_id = $feed->aggregator_feed_id;
        $last_item_time = self::getLastItemTime($feed->aggregator_feed_id);
        $total = count($data);
        $inserted = 0;
        $rewritten = 0;

        foreach($data as $entry)
        {
            if($entry['article_date'] > $last_item_time) // only new items based on item date
            {
                $article = new self();
                $article->aggregator_feed_id = $feed->aggregator_feed_id;
                $article->populate($entry);

                // check item md5 (rewrite existing item)

                $is_rewrite = false;

                if(is_object($_i = self::getByArticleMd5(
                    $feed->aggregator_feed_id, $article->article_md5)))
                {
                    $article->setPrimaryKey($_i->getPrimaryKey());
                    $is_rewrite = true;
                }

                try
                {
                    $article->save();
                    $is_rewrite ? $rewritten++ : $inserted++;
                }
                catch(B_Exception $_e)
                {
                    $saved = false;
                    self::rollback();
                    $_m = "new aggregator feed article failed from " .
                          "article link (" . $article->article_link . ")";
                    $_d = array ('method' => __METHOD__);
                    B_Exception::forward($_m, E_USER_ERROR, $_e, $_d);
                }
            }
        }

        self::commit();

        $_m = "aggregator feed id (" . $feed_id . ") " .
              "updated with a total of (" . $total . ") articles, " .
              "inserted (" . $inserted . ") " .
              "and rewritten (" . $rewritten . ")";
        $_d = array ('method' => __METHOD__);
        B_Log::write($_m);

        return ($inserted + $rewritten);
    }
}
