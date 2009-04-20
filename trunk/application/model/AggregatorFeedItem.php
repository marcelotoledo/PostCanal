<?php

/**
 * AggregatorFeedItem model class
 * 
 * @category    Blotomate
 * @package     Model
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
class AggregatorFeedItem extends B_Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected static $table_name = 'model_aggregator_feed_item';

    /**
     * Table structure
     *
     * @var array
     */
    protected static $table_structure = array (
		'aggregator_feed_item_id' => array ('type' => 'integer','size' => 0,'required' => false),
		'aggregator_feed_id' => array ('type' => 'integer','size' => 0,'required' => true),
		'item_md5' => array ('type' => 'string','size' => 32,'required' => true),
		'item_date' => array ('type' => 'date','size' => 0,'required' => false),
		'item_link' => array ('type' => 'string','size' => 0,'required' => true),
		'item_title' => array ('type' => 'string','size' => 0,'required' => true),
		'item_author' => array ('type' => 'string','size' => 200,'required' => true),
		'item_content' => array ('type' => 'string','size' => 0,'required' => true),
		'created_at' => array ('type' => 'date','size' => 0,'required' => false));



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
    protected static $primary_key_name = 'aggregator_feed_item_id';


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
        if($this->item_md5 == null)
        {
            if(array_key_exists('item_link', $data) && strlen($data['item_link']) > 0)
            {
                $this->item_md5 = md5($data['item_link']);
            }

            if($this->item_md5 == null)
            {
                $item_md5 = md5(L_Utility::randomString(8));
            }
        }

        parent::populate($data);
    }

    /**
     * Find AggregatorFeedItem with an encapsulated SELECT command
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
     * Get AggregatorFeedItem with SQL
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
     * Find AggregatorFeedItem by primary key
     *
     * @param   integer $id    Primary key value
     *
     * @return  AggregatorFeedItem|null 
     */
    public static function findByPrimaryKey($id)
    {
        return current(self::find(array(self::$primary_key_name => $id)));
    }

    /**
     * Find AggregatorFeedItem by Feed
     *
     * @param   integer $feed       AggregatorFeed ID
     * @param   integer $limit      Limit
     * @param   integer $offset     Offset
     * @param   boolean $assoc
     *
     * @return  AggregatorFeedItem|array|null 
     */
    public static function findByFeed($feed, $limit=25, $offset=0, $assoc=false)
    {
        return $assoc ? 
            self::_findByFeed_Assoc($feed, $limit, $offset) :
            self::_findByFeed_Obj($feed, $limit, $offset);
    }

    protected static function _findByFeed_Obj($feed, $limit=25, $offset=0)
    {
        return self::find(
            array('aggregator_feed_id' => $feed),
            array('created_at DESC'),
            $limit,
            $offset
        );
    }

    protected static function _findByFeed_Assoc($feed, $limit=25, $offset=0)
    {
        $_s = "SELECT item_md5 AS item, item_date AS date, item_link AS link, item_title AS title, item_author as author, item_content AS content FROM " . self::$table_name . " WHERE aggregator_feed_id = ? ORDER BY created_at DESC";

        if(($limit = intval($limit)) > 0)
        {
            $_s .= " LIMIT " . $limit;

            if(($offset = intval($offset)) > 0)
            {
                $_s .= ", " . $offset;
            }
        }

        return self::select($_s, array($feed), PDO::FETCH_ASSOC);
    }
}
