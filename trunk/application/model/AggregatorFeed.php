<?php

/**
 * AggregatorFeed model class
 * 
 * @category    Blotomate
 * @package     Application Model
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class AggregatorFeed extends B_Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected static $table_name = 'model_aggregator_feed';

    /**
     * Table structure
     *
     * @var array
     */
    protected static $table_structure = array (
		'aggregator_feed_id' => array ('type' => 'integer','size' => 0,'required' => false),
		'feed_url' => array ('type' => 'string','size' => 0,'required' => true),
		'feed_url_md5' => array ('type' => 'string','size' => 32,'required' => true),
		'feed_link' => array ('type' => 'string','size' => 0,'required' => true),
		'feed_title' => array ('type' => 'string','size' => 100,'required' => true),
		'feed_description' => array ('type' => 'string','size' => 0,'required' => true),
		'feed_modified' => array ('type' => 'string','size' => 100,'required' => true),
		'feed_update_time' => array ('type' => 'integer','size' => 0,'required' => false),
		'feed_status' => array ('type' => 'string','size' => 3,'required' => false),
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
    protected static $primary_key_name = 'aggregator_feed_id';


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
     * Get AggregatorFeed by primary key
     *
     * @param   integer $id    Primary key value
     *
     * @return  AggregatorFeed|null 
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
            $this->feed_url_md5 = md5($this->feed_url);
        }

        return parent::save();
    }

    /**
     * Find by URL
     *
     * @param   string  $url
     * @param   integer $lifetime   feed url lifetime
     * @return  array
     */
    public static function findAssocByURL($url, $lifetime=86400)
    {
        $_s = "SELECT a.feed_url, b.feed_title, b.feed_description 
               FROM model_aggregator_feed_discover AS a
               LEFT JOIN model_aggregator_feed AS b 
               ON (a.feed_url_md5 = b.feed_url_md5)
               WHERE a.url_md5 = ? AND (UNIX_TIMESTAMP(a.updated_at) + ?) > 
               UNIX_TIMESTAMP(UTC_TIMESTAMP())";
        $_d = array(md5($url), $lifetime);
        return self::select($_s, $_d, PDO::FETCH_ASSOC);
    }

    /**
     * Get by Feed URL
     *
     * @param   string                  $url        Feed URL
     * @return  AggregatorFeed|null 
     */
    public static function getByURL($url)
    {
        return current(self::select(
            "SELECT * FROM " . self::$table_name . " WHERE feed_url_md5 = ?", 
            array(md5($url)), PDO::FETCH_CLASS, get_class()));
    }

    /**
     * Find outdated feeds (need update)
     *
     * @param   integer         $limit
     * @return  AggregatorFeed
     */
    public static function findOutdated($limit=10)
    {
        $sql = "SELECT aggregator_feed_id, feed_url, feed_modified
                FROM " . self::$table_name . "
                WHERE (feed_update_time + UNIX_TIMESTAMP(updated_at)) < 
                       UNIX_TIMESTAMP(UTC_TIMESTAMP())
                ORDER BY (feed_update_time + UNIX_TIMESTAMP(updated_at)) ASC
                LIMIT " . intval($limit);

        return self::select($sql, array(), PDO::FETCH_ASSOC);
    }

    /**
     * Insert feed (raw)
     *
     * @param   array           $data
     * @param   integer         $inserted
     * @return  AggregatorFeed
     */
    public static function rawInsert($data, &$inserted=null)
    {
        $url = $data['feed_url'];

        self::transaction();

        if(($feed = self::getByURL($url)) == null)
        {
            $feed = new self();
            $feed->populate($data);

            try
            {
                $feed->save();
            }
            catch(B_Exception $_e)
            {
                self::rollback();
                $_m = "new aggregator feed failed";
                $_d = array ('method' => __METHOD__);
                B_Exception::forward($_m, E_USER_ERROR, $_e, $_d);
            }
        }

        self::commit();

        if(array_key_exists('articles', $data) && is_object($feed))
        {
            $inserted = AggregatorFeedArticle::rawInsert($feed, $data['articles']);
        }

        return $feed;
    }

    /**
     * Update feed
     *
     * @param   integer         $id         AggregatorFeed ID
     * @param   array           $data
     * @param   integer         $updated
     * 
     * @return  AggregatorFeed
     */
    public static function rawUpdate($id, $data, &$updated=null)
    {
        self::transaction();
        
        if(is_object(($feed = self::getByPrimaryKey($id))))
        {
            $feed->populate($data);

            try
            {
                $feed->save();
            }
            catch(B_Exception $_e)
            {
                self::rollback();
                $_m = "aggregator feed update failed";
                $_d = array ('method' => __METHOD__);
                B_Exception::forward($_m, E_USER_ERROR, $_e, $_d);
            }
        }

        self::commit();

        if(array_key_exists('articles', $data) && is_object($feed))
        {
            $updated = AggregatorFeedArticle::rawInsert($feed, $data['articles']);
        }

        return $feed;
    }

    /**
     * Discover feeds from URL
     *
     * @param   string  $url
     * @return  array
     */
    public static function discover($url)
    {
        if(count(($feeds = self::findAssocByURL($url))) == 0)
        {
            /* request feeds to webservice */

            $feeds = array();
            $client = new A_WebService();
            $discover = ((array) $client->feed_discover(array('url' => $url)));

            if(count($discover) == 1)
            {
                if(is_object(($feed = self::rawInsert(current($discover)))))
                {
                    $feeds[] = $feed->dump(array('feed_url', 'feed_title', 'feed_description'));
                }
            }
            /* if discover return more than one feed
             * do not insert on database, but give it back to user */
            else
            {
                $feeds = $discover;
            }

            foreach($feeds AS $f)
            {
                $_s = "INSERT INTO model_aggregator_feed_discover 
                       (url, url_md5, feed_url, feed_url_md5, updated_at) 
                       VALUES (?, ?, ?, ?, UTC_TIMESTAMP()) 
                       ON DUPLICATE KEY UPDATE updated_at=UTC_TIMESTAMP()";
                $_d = array($url, md5($url), $f['feed_url'], md5($f['feed_url']));
                self::execute($_s, $_d);
            }
        }

        return $feeds;
    }
}
