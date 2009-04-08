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
    protected static $table_structure = array();

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
     * Discover feeds from URL
     *
     * @param   string  $url
     * @return  AggregatorFeed|null
     */
    public static function discover($url)
    {
        $um5 = md5($url);

        if(count(($feeds = self::findByURL($url))) == 0)
        {
            /* request feeds to webservice */

            $client = new L_WebService();
            $results = $client->feed_discover(array('url' => $url));
            $results_len = count($results);

            for($i=0;$i<$results_len;$i++)
            {
                $feed_url = $results[$i]['url'];

                self::transaction();

                if(($feed = self::findByFeedURL($feed_url)) == null)
                {
                    $feed = new self();
                    $feed->feed_url = $feed_url;
                    $feed->feed_url_md5 = md5($feed_url);
                    $feed->feed_title = $results[$i]['title'];
                    $feed->feed_description = $results[$i]['description'];
                    $feed->feed_link = $results[$i]['link'];
                    $feed->feed_date = $results[$i]['date'];

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

                /* add feed to feed list */

                $feeds[] = $feed;

                self::commit();

                /* save feed items */

                $entries = is_array($results[$i]['entries']) ? 
                    $results[$i]['entries'] :
                    array();

                self::transaction();

                foreach($entries as $entry)
                {
                    $item = new AggregatorFeedItem();
                    $item->aggregator_feed_id = $feed->aggregator_feed_id;
                    $item->item_id = $entry['id'];
                    $item->item_title = $entry['title'];
                    $item->item_link = $entry['link'];
                    $item->item_description = $entry['description'];
                    $item->item_date = $entry['date'];

                    $item_md5 = null;
                    if(strlen($item->item_link) > 0) $item_md5 = md5($item->item_link);
                    if($item_md5 == null) $item_md5 = md5(L_Utility::randomString(8));
                    $item->item_md5 = $item_md5;

                    try
                    {
                        $item->save();
                    }
                    catch(B_Exception $_e)
                    {
                        self::rollback();
                        $_m = "new aggregator feed item failed from " .
                              "item link (" . $item->item_link . ")";
                        $_d = array ('method' => __METHOD__);
                        B_Exception::forward($_m, E_USER_ERROR, $_e, $_d);
                    }
                }

                self::commit();

                /* save feed url */

                self::transaction();

                $sql = "SELECT COUNT(*) AS total " .
                       "FROM model_aggregator_feed_url " .
                       "WHERE aggregator_feed_id = ? AND url_md5 = ?";

                $_d = array($feed->aggregator_feed_id, $um5);
                $_r = current(self::select($sql, $_d));

                if($_r->total == 0)
                {
                    $sql = "INSERT INTO model_aggregator_feed_url " .
                           "(aggregator_feed_id, url, url_md5) VALUES (?, ?, ?)";
                    $_d = array($feed->aggregator_feed_id, $url, $um5);

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
            }
        }

        return $feeds;
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
        return current(self::find(array('feed_url_md5' => md5($feed_url))));
    }
}
