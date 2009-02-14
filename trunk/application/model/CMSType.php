<?php

/**
 * CMSType model class
 * 
 * @category    Blotomate
 * @package     Model
 */
class CMSType extends AB_Model
{
    /**
     * Discovery constants
     */
    const DISCOVERY_URL    = "url";
    const DISCOVERY_HEADER = "header";
    const DISCOVERY_HTML   = "html";


    /**
     * Table name
     *
     * @var string
     */
    protected static $table_name = 'cms_type';

    /**
     * Sequence name
     *
     * @var string
     */
    protected static $sequence_name = 'cms_type_seq';

    /**
     * Primary key name
     *
     * @var string
     */
    protected static $primary_key_name = 'cms_type_id';

    /**
     * Discovery table name
     *
     * @var string
     */
    protected static $discovery_table_name = 'cms_type_discovery';

    /**
     * CMSType plugin info (DEPRECATED)
     *
     * @var Object
     */
    protected $plugin_info = array();


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
     * Get CMSType plugin info (DEPRECATED)
     *
     * @param   string  $url    Base URL
     * @throws  AB_Exception
     * @return  array
     */
    public function getPluginInfo($url)
    {
        if($this->isNew())
        {
            $message = "new model cannot determine cms type and get it's info";
            AB_Exception::throwNew(
                "a new cms type can not be used " . 
                "to obtain information from the plugin",
                E_USER_ERROR);
        }

        if(count($this->plugin_info) == 0)
        {
            $this->plugin_info = self::loadPluginInfo(
                $this->name, $this->version, $url);
        }

        return $this->plugin_info;
    }

    /**
     * Find CMSType with an encapsulated SELECT command
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
     * Get CMSType with SQL
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
     * Find CMSType by primary key
     *
     * @param   integer $id    Primary key value
     *
     * @return  CMSType|null 
     */
    public static function findByPrimaryKey($id)
    {
        return current(self::find(array(self::$primary_key_name => $id)));
    }

    /* CMS TYPE DISCOVERY */

    /**
     * Discovery CMS type
     *
     * @param   string          $url
     * @param   string          $headers
     * @param   string          $html
     * @return  CMSType|null
     */
    public static function discovery($url, $headers, $html)
    {
        $cms_type = null;
        $rules = array();
        $types = array();

        /* url */

        $results = self::discoveryByURL($url, $types);
        $rules = array_merge($rules, array_unique(array_keys($results)));
        $types = array_unique(array_values($results));

        /* headers */

        $results = self::discoveryByHeaders($headers, $types);
        $rules = array_merge($rules, array_unique(array_keys($results)));
        $types = array_unique(array_values($results));

        /* body */

        $results = self::discoveryByHTML($html, $types);
        $rules = array_merge($rules, array_unique(array_keys($results)));
        $types = array_unique(array_values($results));

        /* more than one cms type == warning */

        if(count($types) > 1)
        {
            $message = "cms types [" . implode(", ", $types) . "] " . 
                       "have conflicting discovery rules " . 
                       "[" . implode(", ", $rules) . "]";

            AB_Log::write($message, E_USER_WARNING);
        }

        $type = current($types);

        if(!empty($type))
        {
            $total = self::discoveryTotalRulesForCMS($type);

            /* if all rules passed for current type, then get cms type */

            if(count($rules) >= $total)
            {
                $cms_type = self::findByPrimaryKey($type);
            }

            /* log 'not all rules passed' type */

            else
            {
                $message = "cms type (" . $type . ") did not matched " .
                           "a total of (" . $total . ") discovery rules. " .
                           "matched only [" . implode(", ", $rules) . "] ";

                AB_Log::write($message, E_USER_WARNING);
            }
        }

        return $cms_type;
    }

    /**
     * Find discovery rules by URL
     * 
     * @param   string      $url
     * @param   array       $types      CMS type IDs array
     * @param   boolean     $regexp
     * @return  array
     */
    protected static function discoveryByURL(
        $url, $types=array(), $regexp=true)
    {
        return self::discoveryByNameValue(
            self::DISCOVERY_URL, $url, $types, $regexp);
    }

    /**
     * Find discovery rules by Headers
     * 
     * @param   array       $headers
     * @param   array       $types      CMS type IDs array
     * @param   boolean     $regexp
     * @return  array
     */
    protected static function discoveryByHeaders(
        $headers, $types=array(), $regexp=true)
    {
        $results = array();

        foreach($headers as $name => $value)
        {
            if(!empty($value))
            {
                if(is_array($value)) $value = implode("; ", $value);

                $header = strtolower($name . ": " . $value);

                $results = array_merge(
                    $results, self::discoveryByNameValue(
                        self::DISCOVERY_HEADER, $header, $types, $regexp));
            }
        }

        return $results;
    }

    /**
     * Find discovery rules by HTML
     * 
     * @param   string      $html
     * @param   array       $types      CMS type IDs array
     * @param   boolean     $regexp
     * @return  array
     */
    protected static function discoveryByHTML(
        $html, $types=array(), $regexp=true)
    {
        return self::discoveryByNameValue(
           self::DISCOVERY_HTML, $html, $types, $regexp);
    }

    /**
     * Find discovery rules by name [and value [regexp]]
     *
     * @param   string  $name
     * @param   string  $value
     * @param   array   $types      CMS type IDs array
     * @param   boolean $regexp
     *
     * @return  array
     */
    protected static function discoveryByNameValue(
        $name, $value=null, $types=array(), $regexp=false)
    {
        $sql = "SELECT cms_type_discovery_id, cms_type_id " .
               "FROM " . self::$discovery_table_name . " " .
               "WHERE name = ? ";

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

        $results = array();

        foreach(self::select($sql, $conditions) as $i)
        {
            $results[$i->cms_type_discovery_id] = $i->cms_type_id;
        }

        return $results;
    }

    /**
     * Total rules for a CMS type
     *
     * @param   integer     $type       CMS type ID
     * @return  integer                 Total
     */
    protected static function discoveryTotalRulesForCMS($type)
    {
        $sql = "SELECT COUNT(*) AS total FROM cms_type_discovery " . 
               "WHERE cms_type_id = ?";

        $result = current(self::select($sql, array($type)));

        return is_object($result) ? $result->total : 0;
    }

    /* CMS TYPE PLUGIN */

    /**
     * Load CMS Type plugin info (DEPRECATED)
     * 
     * @param   string          $name       Plugin Name
     * @oaram   string          $version    Plugin Version
     * @oaram   string          $url        Base URL
     * @throws  AB_Exception
     * @return  array
     */
    protected static function loadPluginInfo($name, $version, $url)
    {
        $path = APPLICATION_PATH . "/library/CMSType";
        $filename = strtolower($name) . ".py";
        $plugin = null;

        if(!file_exists(($plugin = $path . "/" . $filename)))
        {
            AB_Exception::throwNew(
                "plugin (" . $plugin . ") does not exist",
                E_USER_ERROR);
        }

        $registry = AB_Registry::singleton();
        $python = $registry->python->interpreter->path;

        if(empty($python)) $python = "python";

        /* execute plugin command */

        $command = $python . " " . 
                   $plugin . " " . 
                   escapeshellarg($version) . " " . 
                   escapeshellarg($url);
        $output = array();
        $status = 0;

        exec($command, $output, $status);

        /* log exec command and its return */

        AB_Log::write(
            "exec (" . $command . ") return (" . $status . ")",
            E_USER_NOTICE);

        /* convert output to array */

        $results = array();

        for($i=0; $i<count($output); $i++)
        {
            list($arg1, $arg2) = split("\t", $output[$i]);
            $results[$arg1] = $arg2;
        }

        return $results;
    }
}
