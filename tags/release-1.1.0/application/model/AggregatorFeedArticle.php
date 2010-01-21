<?php

/**
 * AggregatorFeedArticle model class
 * 
 * @category    PostCanal
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
		'keywords' => array ('type' => 'string','size' => 0,'required' => false),
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
     * Generate article MD5 
     */
    protected function makeArticleMd5()
    {
        if(strlen($this->article_md5) == 0 && strlen($this->article_link) > 0)
        {
            $this->article_md5 = md5($this->article_link);
        }
        if(strlen($this->article_md5) == 0)
        {
            $this->article_md5 = md5(L_Utility::randomString(8));
        }
    }

    /**
     * Save model
     *
     * @return  boolean
     */
    public function save()
    {
        if($this->isNew() && strlen($this->article_md5) == 0)
        {
            $this->makeArticleMd5();
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
     * get user blog writing article
     */
    public static function getWritingArticle($user, $blog, $article)
    {
        return current(self::select(
            "SELECT * FROM " . self::$table_name . 
            " WHERE aggregator_feed_id = (
                SELECT aggregator_feed_id
                FROM model_aggregator_feed
                WHERE feed_url_md5 = ?
              ) 
              AND article_md5 = ?",
            array(md5(sprintf(AggregatorFeed::WRITINGS_URL_BASE, $user, $blog)), $article), 
            PDO::FETCH_CLASS, get_class()));
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

        $sql.= "ORDER BY article_date DESC, created_at DESC LIMIT " . intval($limit);

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

        $sql.= "ORDER BY article_date DESC, created_at DESC LIMIT " . intval($limit);

        return self::select($sql, $args, PDO::FETCH_ASSOC);
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
        $inserted = 0;
        $updated = 0;

        foreach($data as $entry)
        {
            $article = new self();
            $article->aggregator_feed_id = $feed->aggregator_feed_id;
            $article->populate($entry);
            $article->makeArticleMd5();

            $keywords = $article->article_title . " " . $article->article_content;
            L_Utility::keywords($keywords);
            $article->keywords = $keywords;

            $is_new = false;
            $is_update = false;

            if(is_object($_i = self::getByArticleMd5(
                $feed->aggregator_feed_id, $article->article_md5)))
            {
                if($article->article_date != $_i->article_date)
                {
                    $is_update = true;
                    $article->setPrimaryKey($_i->getPrimaryKey());
                }
            }
            else
            {
                $is_new = true;
            }

            try
            {
                if($is_new || $is_update)
                {
                    $article->save();
                    $is_new ? $inserted++ : $updated++;
                }
            }
            catch(B_Exception $_e)
            {
                self::rollback();
                $_m = "new aggregator feed article failed from " .
                      "article link (" . $article->article_link . ")";
                $_d = array ('method' => __METHOD__);
                B_Exception::forward($_m, E_ERROR, $_e, $_d);
            }
        }

        $feed->article_total_count += $inserted;
        $feed->save();

        self::commit();

        $_t = $inserted + $updated;

        $_m = "aggregator feed id (" . $feed_id . ") " .
              "updated with a total of (" . $_t . ") articles : " .
              "inserted (" . $inserted . ") " .
              "and updated (" . $updated . ")";
        $_d = array ('method' => __METHOD__);
        B_Log::write($_m, E_NOTICE);

        return ($inserted + $updated);
    }
}
