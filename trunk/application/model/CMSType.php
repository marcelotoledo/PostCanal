<?php

/**
 * CMSType model class
 * 
 * @category    Blotomate
 * @package     Model
 * @author      Rafael Castilho <rafael@castilho.biz>
 * @examples
 *
 * DISCOVERY
 *
 * url                 http:\/\/.+\.wordpress\.com
 * header              server:\ wordpress\.com
 * html                div.+id.+wrapper
 * html                <meta[^>]+(content)+[^>]+(wordpress\.com)+[^>]+>
 *
 */
class CMSType extends AB_Model
{
    /**
     * Discovery constants
     */
    const DISCOVERY_REQUIRED     = "required";
    const DISCOVERY_URL          = "url";
    const DISCOVERY_URL_REPLACE  = "url_replace";
    const DISCOVERY_URL_MATCH    = "url_match";
    const DISCOVERY_HEADER       = "header";
    //    DISCOVERY_HEADER_REPLACE: is not necessary because headers have a simple format
    const DISCOVERY_HEADER_MATCH = "header_match";
    const DISCOVERY_HTML         = "html";
    const DISCOVERY_HTML_REPLACE = "html_replace";
    const DISCOVERY_HTML_MATCH   = "html_match";

    /**
     * Configuration constants
     */
    const CONFIG_MANAGER_URL            = "manager_url";
    const CONFIG_MANAGER_ACTION_URL     = "manager_action_url";
    const CONFIG_MANAGER_INPUT_USERNAME = "manager_input_username";
    const CONFIG_MANAGER_INPUT_PASSWORD = "manager_input_password";

    const CONFIG_MANAGER_HTML_REPLACE   = "manager_html_replace";
    const CONFIG_MANAGER_HTML_MATCH     = "manager_html_match";


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
     * Configuration
     *
     * @var string
     */
    protected $configuration = array();

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
#            throw new AB_Exception(
#                "a new cms type can not be used " . 
#                "to obtain information from the plugin",
#                E_USER_ERROR);
        }

        if(count($this->plugin_info) == 0)
        {
            $this->plugin_info = self::loadPluginInfo(
                $this->name, $this->version, $url);
        }

        return $this->plugin_info;
    }

    /**
     * Get configuration
     *
     * @return  array
     */
    public function getConfiguration()
    {
        if(count($this->configuration) == 0)
        {
            $sql = "SELECT name, value FROM cms_type_configuration " . 
                   "WHERE cms_type_id = ?";

            $results = array();

            foreach(self::select($sql, array($this->cms_type_id)) as $i)
            {
                $results[$i->name] = $i->value;
            }

            $this->configuration = $results;
        }

        return $this->configuration;
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
    public static function discovery(&$url, $headers, &$html)
    {
        $cms_type = null;
        $types = array();
        $results = array();

        /* url */

        $d = self::discoveryByURL($url, $types);
        $types = array_unique(array_keys($d));
        $results[self::DISCOVERY_URL] = $d;

        /* headers */

        $d = self::discoveryByHeaders($headers, $types);
        $types = array_unique(array_keys($d));
        $results[self::DISCOVERY_HEADER] = $d;

        /* body */

        $d = self::discoveryByHTML($html, $types);
        $types = array_unique(array_keys($d));
        $results[self::DISCOVERY_HTML] = $d;

        $type = current($types);

        /* more than one cms type == warning */

        if(count($types) > 1)
        {
            $message = "types {" . implode(", ", $types) . "} " .
                       "have conflicting discovery rules. " .
                       "only (" . $type . ") will be considered";
            $attributes = array('method' => __METHOD__);
            AB_Log::write($message, E_USER_WARNING, $attributes);
        }

        if(!empty($type))
        {
            /* check required rules */

            $unmatched = array();

            foreach(self::discoveryRequiredByType($type) as $r)
            {
                if(!in_array($type, array_keys($results[$r]))) $unmatched[] = $r;
            }

            /* if all rules passed for current type, then get cms type */

            if(count($unmatched) == 0)
            {
                $cms_type = self::findByPrimaryKey($type);
            }

            /* log 'not all rules passed' type */

            else
            {
                $message = "type (" . $type . ") unmatched required " .
                           "discovery rules {" . implode(", ", $unmatched) . "}";
                $attributes = array('method' => __METHOD__);
                AB_Log::write($message, E_USER_WARNING, $attributes);
            }

            /* update url when available */

            $u = self::DISCOVERY_URL;

            if(array_key_exists($u, $results))
                if(array_key_exists($type, $results[$u]))
                    $url = $results[$u][$type];
        }

        return $cms_type;
    }

    /**
     * Discovery by URL
     * 
     * @param   string      $url
     * @param   array       $types      CMS type IDs array
     * @return  array
     */
    protected static function discoveryByURL(&$url, $types=array())
    {
        self::fixURL($url);
        $r = self::discoveryFindRules(self::DISCOVERY_URL_REPLACE, $types);
        $m = self::discoveryFindRules(self::DISCOVERY_URL_MATCH, $types);
        return self::pregFilter($url, $r, $m);
    }

    /**
     * Discovery by headers
     * 
     * @param   array       $headers
     * @param   array       $types      CMS type IDs array
     * @return  array
     */
    protected static function discoveryByHeaders($headers, $types=array())
    {
        $m = self::discoveryFindRules(self::DISCOVERY_HEADER_MATCH, $types);
        $a = array();

        foreach($headers as $k => $v)
        {
            if(!empty($v))
            {
                $h = strtolower($k. ": " . (is_array($v) ? implode("; ", $v) : $v));
                $a = array_merge($a, self::pregFilter($h, array(), $m));
            }
        }

        return $a;
    }

    /**
     * Discovery by HTML
     * 
     * @param   string      $html
     * @param   array       $types      CMS type IDs array
     * @return  array
     */
    protected static function discoveryByHTML(&$html, $types=array())
    {
        self::cleanHTML($html);
        $r = self::discoveryFindRules(self::DISCOVERY_HTML_REPLACE, $types);
        $m = self::discoveryFindRules(self::DISCOVERY_HTML_MATCH, $types);
        return self::pregFilter($html, $r, $m);
    }

    /**
     * Find discovery rules by name
     *
     * @param   string  $name
     * @param   array   $types      CMS type IDs array
     *
     * @return  array
     */
    protected static function discoveryFindRules($name, $types=array())
    {
        $sql = "SELECT cms_type_id, value FROM cms_type_discovery WHERE name = ? ";

        if(count($types) > 0)
        {
            $sql.= "AND cms_type_id IN (" . implode(", ", $types) . ") ";
        }

        $sql.= "ORDER BY cms_type_id, cms_type_discovery_id ASC";

        $results = array();

        foreach(self::select($sql, array($name)) as $i)
        {
            $type = $i->cms_type_id;
            $rules = ((array) unserialize($i->value));
            $results[$type] = (array_key_exists($type, $results)) ?
                array_merge($results[$type], $rules) : 
                $rules;
        }

        return $results;
    }

    /**
     * Required rules for a CMS type
     *
     * @param   integer     $type       CMS type ID
     * @return  integer                 Total
     */
    protected static function discoveryRequiredByType($type)
    {
        $sql = "SELECT value FROM cms_type_discovery " . 
               "WHERE name = ? AND cms_type_id = ?";

        $result = current(self::select($sql, array(self::DISCOVERY_REQUIRED, $type)));

        return is_object($result) ? unserialize($result->value) : array();
    }

    /* MANAGER */

    /**
     * Check manager HTML
     * 
     * @param   string      $html
     * @param   array       $config      CMS Type configuration
     * @return  array
     */
    public static function managerCheckHTML(&$html, &$config)
    {
        self::cleanHTML($html);

        $r = array();
        $m = array();

        $k = self::CONFIG_MANAGER_HTML_REPLACE;
        if(array_key_exists($k, $config)) $r = array(unserialize($config[$k]));

        $k = self::CONFIG_MANAGER_HTML_MATCH;
        if(array_key_exists($k, $config)) $m = array(unserialize($config[$k]));

        return (count(self::pregFilter($html, $r, $m)) > 0);
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
#            throw new AB_Exception(
#                "plugin (" . $plugin . ") does not exist",
#                E_USER_ERROR);
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

##        self::log("exec (" . $command . ") return (" . $status . ")");

        /* convert output to array */

        $results = array();

        for($i=0; $i<count($output); $i++)
        {
            list($arg1, $arg2) = split("\t", $output[$i]);
            $results[$arg1] = $arg2;
        }

        return $results;
    }

    /* UTILITIES */

    /**
     * PREG filter
     * 
     * @param   string      $subject
     * @param   array       $replace    replace rules
     * @param   array       $match      match rules
     * @return  array
     */
    protected function pregFilter($subject, $replace, $match)
    {
        $replaced = array();
    
        foreach($replace as $k => $rules)
        {
            $current = $subject;
    
            foreach($rules as $r)
            {
                $parameters = array_merge($r, array($current));
                $current = call_user_func_array('preg_replace', $parameters);
            }
    
            if(strlen($current) > 0 && ($current != $subject))
            {
                $replaced[$k] = $current;
            }
        }
    
        $results = array();
    
        foreach($match as $k => $rules)
        {
            $current = array_key_exists($k, $replaced) ? $replaced[$k] : $subject;
    
            foreach($rules as $r)
            {
                $parameters = array_merge($r, array($current));
    
                if(call_user_func_array('preg_match', $parameters) > 0)
                {
                    $results[$k] = $current;
                }
            }
        }
    
        return $results;
    }

    /**
     * Fix URL
     * 
     * @param   string      $url
     * @return  string
     */
    protected static function fixURL(&$url)
    {
        $pattern = "#^(.*?//)*([\w\.\d]*)(:(\d+))*(/*)(.*)$#";
        $matches = array();
        preg_match($pattern, $url, $matches);

        $protocol = empty($matches[1]) ? "http://" : $matches[1];
        $address  = empty($matches[2]) ? ""        : $matches[2];
        $port     = empty($matches[3]) ? ""        : $matches[3];
        $resource = empty($matches[6]) ? ""        : $matches[5] . $matches[6];

        $url = $protocol . $address . $port . $resource;
    }

    /**
     * Clean HTML (compact)
     * 
     * @param   string  $html
     * @return  void
     */
    protected static function cleanHTML(&$html)
    {
        $html = preg_replace("/[\r\n]+/", "", $html); // new lines
        $html = preg_replace("/[[:space:]]+/", " ", $html); // spaces
        $html = preg_replace("/>[[:space:]]+</", "><", $html); // spaces
        $html = preg_replace("/>[^<]*</", "><", $html); // tag content
    }
}
