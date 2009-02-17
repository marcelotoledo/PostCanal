<?php

/* CMS type discovery examples:

        NAME                VALUE

        url                 http:\/\/.+\.wordpress\.com
        header              server:\ wordpress\.com
        html                div.+id.+wrapper
        html                <meta[^>]+(content)+[^>]+(wordpress\.com)+[^>]+>
*/

/**
 * CMSTypeDiscovery model class (DEPRECATED)
 * 
 * @category    Blotomate
 * @package     Model
 */
class CMSTypeDiscovery extends AB_Model
{
    /**
     * Discovery name constants
     */
    const NAME_URL    = "url";
    const NAME_HEADER = "header";
    const NAME_HTML   = "html";


    /**
     * Table name
     *
     * @var string
     */
    protected static $table_name = 'cms_type_discovery';

    /**
     * Sequence name
     *
     * @var string
     */
    protected static $sequence_name = 'cms_type_discovery_seq';

    /**
     * Primary key name
     *
     * @var string
     */
    protected static $primary_key_name = 'cms_type_discovery_id';


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
     * Save model
     *
     * @return  boolean
     */
    public function save()
    {
        return parent::_save(self::$sequence_name);
    }

    /**
     * Find CMSTypeDiscovery with an encapsulated SELECT command
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
     * Get CMSTypeDiscovery with SQL
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
        return parent::_insert($sql, $data, self::sequence_name);
    }

    /**
     * Find CMSTypeDiscovery by primary key
     *
     * @param   integer $id    Primary key value
     *
     * @return  CMSTypeDiscovery|null 
     */
    public static function findByPrimaryKey($id)
    {
        return current(self::find(array(self::$primary_key_name => $id)));
    }

    /**
     * Find CMSTypeDiscovery by URL
     * 
     * @param   string      $url
     * @param   array       $types
     * @param   boolean     $regexp
     * @return  array
     */
    public static function findByURL($url, 
                                     $types=array(),
                                     $regexp=true)
    {
        return self::findByNameValue(self::NAME_URL, $url, $types, $regexp);
    }

    /**
     * Find CMSTypeDiscovery by Headers
     * 
     * @param   array       $headers
     * @param   array       $types
     * @param   boolean     $regexp
     * @return  array
     */
    public static function findByHeaders($headers, 
                                         $types=array(),
                                         $regexp=true)
    {
        $results = array();

        foreach($headers as $name => $value)
        {
            if(!empty($value))
            {
                if(is_array($value)) $value = implode("; ", $value);

                $header = strtolower($name . ": " . $value);

                $results = array_merge(
                    $results, self::findByNameValue(
                        self::NAME_HEADER, $header, $types, $regexp));
            }
        }

        return $results;
    }

    /**
     * Find CMSTypeDiscovery by HTML
     * 
     * @param   string      $html
     * @param   array       $types
     * @param   boolean     $regexp
     * @return  array
     */
    public static function findByHTML($html, 
                                      $types=array(),
                                      $regexp=true)
    {
        return self::findByNameValue(self::NAME_HTML, $html, $types, $regexp);
    }

    /**
     * Find CMSTypeDiscovery by name [and value [regexp]]
     *
     * @param   string  $name
     * @param   string  $value
     * @param   array   $discovery
     * @param   boolean $regexp
     *
     * @return  array
     */
    public static function findByNameValue($name, 
                                           $value=null, 
                                           $types=array(),
                                           $regexp=false)
    {
        $sql = " SELECT * FROM " . self::$table_name;
        $sql.= " WHERE name = ? ";
        $conditions = array();
        $conditions[] = $name;

        if(!empty($value)) 
        {
            $sql.= " AND ? " . ($regexp ? '~*' : '=') . " value ";
            $conditions[] = $value;
        }

        if(count($types) > 0)
        {
            $sql.= " AND cms_type_id IN (" . implode(", ", $types) . ") ";
        }

        $sql.= " ORDER BY cms_type_id ASC";

        return self::_selectModel($sql, $conditions, get_class());
    }

    /**
     * Total rules for a CMS type
     *
     * @param   integer     $type       CMS Type ID
     * @return  integer                 Total
     */
    public static function totalRulesForCMS($type)
    {
        $sql = "SELECT COUNT(*) AS total FROM cms_type_discovery " . 
               "WHERE cms_type_id = ?";

        $result = current(self::select($sql, array($type)));

        return is_object($result) ? $result->total : 0;
    }
}
