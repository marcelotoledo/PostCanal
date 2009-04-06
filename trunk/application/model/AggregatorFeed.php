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
     * Cache table name
     *
     * @var string
     */
    protected static $cache_table_name = 'model_aggregator_feed_cache';

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
     * Discover Feeds
     *
     * @param   string  $url
     * @param   string  $url_feed
     * @return  AggregatorFeed|null 
     */
    public static function discover($url, $url_feed=null)
    {
        $table = self::$cache_table_name;
        $data = array();
        $data[] = $url;
        $sql = "SELECT * FROM " . self::$cache_table_name . " " .
               "WHERE expires_in > NOW() AND url_md5 = MD5(?) ";

        if($url_feed != null)
        {
            $sql.= "AND url_feed_md5 = MD5(?)";
            $data[] = $url_feed;
        }

        return ($url_feed == null) ? 
            self::select($sql, $data) : 
            current(self::select($sql, $data));
    }

    /**
     * Register Feeds on discovery table
     *
     * @param   array   $data           array('url' => "...", 'url_feed' => "...")
     * @return  AggregatorFeed|null 
     */
    public static function register($data)
    {
        $pk = str_replace("model_", "", self::$cache_table_name) . "_id";

        if(array_key_exists('url', $data) == false ||
           array_key_exists('url_feed', $data) == false)
        {
            $_m = "url and url_feed keys are not found in data";
            $_d = array('method' => __METHOD__);
            throw new B_Exception($_m, E_USER_ERROR, $_d);
        }

        $um5 = ($data['url_md5'] = md5($data['url']));
        $fm5 = ($data['url_feed_md5'] = md5($data['url_feed']));
        $discovery = self::discover($data['url'], $data['url_feed']);

        /* cache expiration */

        $registry = B_Registry::singleton();
        $config = $registry->application()->aggregator();

        if(($url_life = $config->cacheURL) == null) $url_life = 259200;
        if(($feed_life = $config->cacheFeed) == null) $feed_life = 604800;

        $expires_in = date("Y-m-d H:i:s", time() + 
            (($um5 == $fm5) ? $feed_life : $url_life));


        if(is_object($discovery))
        {
            $sql = "UPDATE " . self::$cache_table_name . " " .
                   "SET expires_in = ? WHERE " . $pk . " = ?";

            self::execute($sql, array($expires_in, $discovery->{$pk}));
        }
        else
        {
            $data['expires_in'] = $expires_in;
            $columns = array_keys($data);
            $sql = "INSERT INTO " . self::$cache_table_name . " " .
                   "(" . implode(", ", $columns) . ") VALUES " .
                   "(?" . str_repeat(", ?", count($columns) - 1) . ")";
            self::insert($sql, array_values($data));
        }
    }

    /**
     * Find by URL
     *
     * @param   string  $url
     * @return  AggregatorFeed|null 
     */
    public static function findByURL($url)
    {
        return current(self::find(array('url_md5' => md5($url))));
    }
}
