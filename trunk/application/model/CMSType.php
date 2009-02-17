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
    const D_URL    = "url";
    const D_HEADER = "header";
    const D_HTML   = "html";

    /**
     * Default attributes constants
     */

    /* manager */

    const A_M_URL             = "manager_url";
    const A_M_FORM_ACTION_URL = "manager_form_action_url";
    const A_M_FORM_INPUT_USR  = "manager_form_input_username";
    const A_M_FORM_INPUT_PWD  = "manager_form_input_password";


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
     * Get default attributes
     *
     * @return  array
     */
    public function getDefaultAttributes()
    {
        $sql = "SELECT name, value FROM cms_type_default_attribute " . 
               "WHERE cms_type_id = ?";

        $conditions = array($this->cms_type_id);

        $results = array();

        foreach(self::select($sql, $conditions) as $i)
        {
            $results[$i->name] = $i->value;
        }

        return $results;
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
            $message = "types {" . implode(", ", $types) . "} have conflicting " .
                       "discovery rules {" . implode(", ", $rules) . "}";
            $attributes = array('method' => __METHOD__);
            AB_Log::write($message, E_USER_WARNING, $attributes);
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
                $message = "type (" . $type . ") did not matched " .
                           "a total of (" . $total . ") discovery rules. " .
                           "matched only {" . implode(", ", $rules) . "}";
                $attributes = array('method' => __METHOD__);
                AB_Log::write($message, E_USER_WARNING, $attributes);
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
            self::D_URL, $url, $types, $regexp);
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
                        self::D_HEADER, $header, $types, $regexp));
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
           self::D_HTML, $html, $types, $regexp);
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
               "FROM cms_type_discovery WHERE name = ? ";

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

    /* MANAGER */

    /**
     * Check manager HTML from manager url
     *
     * @param   string          $html
     * @param   string          $attributes
     * @throws  AB_Exception
     * @return  boolean
     */
    public static function managerHTMLCheck($html, $attributes)
    {
        $html_size = strlen($html);
        $exception_data = array('method' => __METHOD__);

        $keys = array (self::A_M_FORM_ACTION_URL, self::A_M_FORM_INPUT_USR,
                       self::A_M_FORM_INPUT_PWD);

        for($i=0; $i<count($keys); $i++)
        {
            if(!array_key_exists($keys[$i], $attributes))
            {
                $message = "the attributes array " .
                           "not have the index (" . $keys[$i] . ")";
                throw new AB_Exception($message, E_USER_WARNING, $exception_data);
            }
        }

        $check = array();

        $k = self::A_M_FORM_ACTION_URL;
        $value = preg_replace("/[^a-zA-Z0-9]+/", ".+", $attributes[$k]);
        $rgexp = "/<form[^>]+(action)+[^>]+(" . $value . ")+[^>]+>/i";
        $check[$k] = $rgexp;

        $k = self::A_M_FORM_INPUT_USR;
        $value = preg_replace("/[^a-zA-Z0-9]+/", ".+", $attributes[$k]);
        $rgexp = "/<input[^>]+(name)+[^>]+(" . $value . ")+[^>]+>/i";
        $check[$k] = $rgexp;

        $k = self::A_M_FORM_INPUT_PWD;
        $value = preg_replace("/[^a-zA-Z0-9]+/", ".+", $attributes[$k]);
        $rgexp = "/<input[^>]+(name)+[^>]+(" . $value . ")+[^>]+>/i";
        $check[$k] = $rgexp;


        foreach($check as $k=>$r)
        {
            if(preg_match($r, $html) == 0)
            {
                $message = "failed to match the expression (" . $r . ") " .
                           "for attribute (" . $k . "=" . $attributes[$k] . ") " .
                           "and the html document with " .
                           "(" . $html_size . ") bytes in size";
                throw new AB_Exception($message, E_USER_WARNING, $exception_data);
            }
        }

        return true;
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
}
