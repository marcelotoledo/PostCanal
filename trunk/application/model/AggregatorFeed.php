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
     * Discover Feeds
     *
     * @param   string  $url
     * @param   string  $url_feed
     * @return  AggregatorFeed|null 
     */
    public static function discover($url, $url_feed=null, $expiration=null)
    {
        $table = str_replace("_feed", "_discovery", self::$table_name);
        $data = array();
        $data[] = $url;
        $sql = "SELECT * FROM " . $table . " WHERE url_md5 = MD5(?)";

        if($url_feed != null)
        {
            $sql.= " AND url_feed_md5 = MD5(?)";
            $data[] = $url_feed;
        }

        if($expiration != null)
        {
            $sql.= "AND updated_at > ?";
            $data[] = $expiration;
            $data[] = $expiration;
        }

        return ($url_feed == null) ? 
            self::select($sql, $data) : 
            current(self::select($sql, $data));
    }

    /**
     * Register Feeds on discovery table
     *
     * @param   array   $data
     * @return  AggregatorFeed|null 
     */
    public static function register($data)
    {
        $table = str_replace("_feed", "_discovery", self::$table_name);
        $pk = str_replace("_feed", "_discovery", self::$primary_key_name);

        if(($k_url = array_key_exists('url', $data)))
        {
            $data['url_md5'] = md5($data['url']);
        }
        if(($k_url_feed = array_key_exists('url_feed', $data)))
        {
            $data['url_feed_md5'] = md5($data['url_feed']);
        }

        $discovery = null;

        if($k_url && $k_url_feed)
        {
            $discovery = self::discover($data['url'], $data['url_feed']);
        }

        if(is_object($discovery))
        {
            $sql = "UPDATE " . $table . " SET updated_at=NOW() " .
                   "WHERE " . $pk . " = ?";
            self::execute($sql, array($discovery->{$pk}));
        }
        else
        {
            $columns = array_keys($data);
            $sql = "INSERT INTO " . $table . " " . 
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
