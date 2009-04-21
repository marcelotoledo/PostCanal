<?php

/**
 * AggregatorFeed model class
 * 
 * @category    Blotomate
 * @package     Model
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
		'feed_md5' => array ('type' => 'string','size' => 32,'required' => true),
		'feed_url' => array ('type' => 'string','size' => 0,'required' => true),
		'feed_link' => array ('type' => 'string','size' => 0,'required' => true),
		'feed_title' => array ('type' => 'string','size' => 100,'required' => true),
		'feed_description' => array ('type' => 'string','size' => 0,'required' => true),
		'feed_modified' => array ('type' => 'string','size' => 100,'required' => false),
        'feed_update_time' => array ('type' => 'integer', 'size' => 0, 'required' => false),
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
     * Populate model data
     *
     * @param   array   $data
     */
    public function populate($data)
    {
        if(array_key_exists('feed_url', $data))
        {
            $this->feed_md5 = md5($data['feed_url']);
        }

        parent::populate($data);
    }

    /**
     * Save model
     *
     * @return  boolean
     */
    public function save()
    {
        $this->updated_at = time(); // required together with update_time

        return parent::save();
    }

    /**
     * Get last item time
     *
     * @return  integer
     */
    public function getLastItemTime()
    {
        $sql = "SELECT UNIX_TIMESTAMP(item_date) AS last_item_time " .
               "FROM model_aggregator_feed_item " .
               "WHERE aggregator_feed_id = ? " .
               "ORDER BY item_date DESC LIMIT 1";

        $result = current(self::select($sql, array($this->aggregator_feed_id)));

        return is_object($result) ? $result->last_item_time : 0;
    }

    /**
     * Find AggregatorFeed with an encapsulated SELECT command
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
     * Get AggregatorFeed with SQL
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
     * Find AggregatorFeed by primary key
     *
     * @param   integer $id    Primary key value
     *
     * @return  AggregatorFeed|null 
     */
    public static function findByPrimaryKey($id)
    {
        return current(self::find(array(self::$primary_key_name => $id)));
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
        $url_md5 = md5($url);

        self::transaction();

        if(($feed = self::findByFeedURL($url)) == null)
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

        if(array_key_exists('entries', $data) && is_object($feed))
        {
            $inserted = AggregatorFeedItem::rawInsert($feed, $data['entries']);
        }

        /* save feed url */

        self::transaction();

        $sql = "SELECT COUNT(*) AS total " .
               "FROM model_aggregator_feed_url " .
               "WHERE aggregator_feed_id = ? AND url_md5 = ?";

        $_d = array($feed->aggregator_feed_id, $url_md5);
        $_r = current(self::select($sql, $_d));

        if($_r->total == 0)
        {
            $sql = "INSERT INTO model_aggregator_feed_url " .
                   "(aggregator_feed_id, url, url_md5) VALUES (?, ?, ?)";
            $_d = array($feed->aggregator_feed_id, $url, $url_md5);

            try
            {
                self::execute($sql, $_d);
            }
            catch(B_Exception $_e)
            {
                self::rollback();
                $_m = "new aggregator feed url failed";
                $_d = array ('method' => __METHOD__);
                B_Exception::forward($_m, E_USER_ERROR, $_e, $_d);
            }
        }

        self::commit();

        return $feed;
    }

    /**
     * Update feed
     *
     * @param   integer         $id         AggregatorFeed ID
     * @param   array           $data
     * @param   integer         $inserted
     * @return  AggregatorFeed
     */
    public static function rawUpdate($id, $data, &$inserted=null)
    {
        #self::transation();
        
        if(is_object(($feed = self::findByPrimaryKey($id))))
        {
            $feed->populate($data);

            $entries = array();

            try
            {
                $feed->save();
            }
            catch(B_Exception $_e)
            {
                #self::rollback();
            }
        }

        #self::commit();

        if(array_key_exists('entries', $data) && is_object($feed))
        {
            $inserted = AggregatorFeedItem::rawInsert($feed, $data['entries']);
        }

        return $feed;
    }

    /**
     * Find by URL
     *
     * @param   string  $url
     * @return  AggregatorFeed|null 
     */
    public static function findByURL($url)
    {
        $sql = "SELECT f.* FROM " . self::$table_name . " AS f " .
               "LEFT JOIN model_aggregator_feed_url AS u " .
               "ON f.aggregator_feed_id = u.aggregator_feed_id " .
               "WHERE u.url_md5 = MD5(?)";

        return self::selectModel($sql, array($url));
    }

    /**
     * Find by Feed URL
     *
     * @param   string  $url
     * @return  AggregatorFeed|null 
     */
    public static function findByFeedURL($feed_url)
    {
        return current(self::find(array('feed_md5' => md5($feed_url))));
    }

    /**
     * Get feed that need update
     *
     * @return  AggregatorFeed
     */
    public static function findNeedUpdate()
    {
        $sql = "SELECT * FROM model_aggregator_feed " .
               "WHERE (feed_update_time + UNIX_TIMESTAMP(updated_at)) < UNIX_TIMESTAMP(UTC_TIMESTAMP()) " .
               "ORDER BY (feed_update_time + UNIX_TIMESTAMP(updated_at)) ASC LIMIT 1";

        return current(self::selectModel($sql));
    }

    /**
     * Discover feeds from URL
     *
     * @param   string  $url
     * @return  array
     */
    public static function discover($url)
    {
        if(count(($feeds = self::findByURL($url))) == 0)
        {
            /* request feeds to webservice */

            $feeds = array();
            $client = new L_WebService();

            foreach($client->feed_discover(array('url' => $url)) as $data)
            {
                $feeds[] = self::rawInsert($data);
            }
        }

        return $feeds;
    }
}
