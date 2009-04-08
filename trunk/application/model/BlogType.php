<?php

/**
 * BlogType model class
 * 
 * @category    Blotomate
 * @package     Model
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
class BlogType extends B_Model
{
    /**
     * Blog discovery constants
     */
    const D_URL_REPLACE    = "url_replace";
    const D_URL_MATCH      = "url_match";
    const D_HEADER_REPLACE = "header_replace"; // not implemented
    const D_HEADER_MATCH   = "header_match";
    const D_HTML_REPLACE   = "html_replace";
    const D_HTML_MATCH     = "html_match";
    const D_HTML_UNMATCH   = "html_unmatch"; // TODO

    /**
     * Configuration constants
     */
    const C_MANAGER_URL            = "manager_url";
    const C_MANAGER_ACTION_URL     = "manager_action_url";
    const C_MANAGER_INPUT_USERNAME = "manager_input_username";
    const C_MANAGER_INPUT_PASSWORD = "manager_input_password";

    const C_MANAGER_HTML_REPLACE   = "manager_html_replace";
    const C_MANAGER_HTML_MATCH     = "manager_html_match";


    /**
     * Table name
     *
     * @var string
     */
    protected static $table_name = 'model_blog_type';

    /**
     * Table structure
     *
     * @var array
     */
    protected static $table_structure = array (
		'blog_type_id' => array ('type' => 'integer','size' => 0,'required' => true),
		'name' => array ('type' => 'string','size' => 50,'required' => true),
		'version' => array ('type' => 'string','size' => 50,'required' => true),
		'maintenance' => array ('type' => 'boolean','size' => 0,'required' => false),
		'enabled' => array ('type' => 'boolean','size' => 0,'required' => false));

    /**
     * Sequence name
     *
     * @var string
     */
    protected static $sequence_name = '';

    /**
     * Primary key name
     *
     * @var string
     */
    protected static $primary_key_name = 'blog_type_id';

    /**
     * Configuration
     *
     * @var string
     */
    protected $configuration = array();

    /**
     * BlogType plugin info (DEPRECATED)
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
     * Get BlogType plugin info (DEPRECATED)
     *
     * @param   string  $url    Base URL
     * @throws  B_Exception
     * @return  array
     */
    public function getPluginInfo($url)
    {
        if($this->isNew())
        {
#            throw new B_Exception(
#                "a new blog type can not be used " . 
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
            $sql = "SELECT name, value FROM " .
                   self::$table_name . "_configuration WHERE blog_type_id = ?";

            $results = array();

            foreach(self::select($sql, array($this->blog_type_id)) as $i)
            {
                $results[$i->name] = $i->value;
            }

            $this->configuration = $results;
        }

        return $this->configuration;
    }

    /**
     * Find BlogType with an encapsulated SELECT command
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
     * Get BlogType with SQL
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
     * Find BlogType by primary key
     *
     * @param   integer $id    Primary key value
     *
     * @return  BlogType|null 
     */
    public static function findByPrimaryKey($id)
    {
        return current(self::find(array(self::$primary_key_name => $id)));
    }

    /* Blog TYPE DISCOVERY */

    /**
     * Discover Blog type
     *
     * @param   string          $url
     * @param   string          $headers
     * @param   string          $html
     * @return  BlogType|null
     */
    public static function discover(&$url, $headers, &$html)
    {
        $blog_type = null;

        $types = array();
        $types = self::discoverByURL($url, $types);
        $types = self::discoverByHeaders($headers, $types);
        $types = self::discoverByHTML($html, $types);

        $type = current($types);

        /* more than one blog type == warning */

        if(count($types) > 1)
        {
            $_m = "types {" . implode(", ", $types) . "} have conflicting " . 
                  "discovery rules. only (" . $type . ") will be considered";
            $_d = array('method' => __METHOD__);
            B_Log::write($_m, E_USER_WARNING, $_d);
        }

        if(!empty($type))
        {
            $blog_type = self::findByPrimaryKey($type);
        }

        return $blog_type;
    }

    /**
     * Discover by URL
     * 
     * @param   string      $url
     * @param   array       $types      Blog type IDs array
     * @return  array
     */
    protected static function discoverByURL(&$url, $types=array())
    {
        L_Utility::fixURL($url);

        $r = self::discoveryRules(self::D_URL_REPLACE, $types);
        $m = self::discoveryRules(self::D_URL_MATCH, $types);

        $k = array();
        $k = array_merge($k, array_keys($r));
        $k = array_merge($k, array_keys($m));

        $k = array_unique($k);

        $results = array();

        foreach($k as $i)
        {
            $a = $url;
            $b = array_key_exists($i, $r) ? $r[$i] : array();
            $c = array_key_exists($i, $m) ? $m[$i] : array();
            $n = count($c);

            if(L_Utility::preg($a, $b, $c) == $n && $n > 0)
            {
                $url = $a;
                $results[] = $i;
            }
        }

        return (count($results) > 0) ? array_unique($results) : $types;
    }

    /**
     * Discover by headers
     * 
     * @param   array       $headers
     * @param   array       $types      Blog type IDs array
     * @return  array
     */
    protected static function discoverByHeaders($headers, $types=array())
    {
        $m = self::discoveryRules(self::D_HEADER_MATCH, $types);
        $a = array();

        foreach($headers as $k => $v)
        {
            if(!empty($v))
            {
                $h = strtolower($k. ": " . (is_array($v) ? implode("; ", $v) : $v));

                foreach($m as $type => $r)
                {
                    $n = count($r);

                    if(L_Utility::preg($h, array(), $r) == $n && $n > 0)
                    {
                        $a = array_merge($a, array($type));
                    }
                }
            }
        }

        return (count($a) > 0) ? array_unique($a) : $types;
    }

    /**
     * Discover by HTML
     * 
     * @param   string      $html
     * @param   array       $types      Blog type IDs array
     * @return  array
     */
    protected static function discoverByHTML(&$html, $types=array())
    {
        L_Utility::compactHTML($html);

        $r = self::discoveryRules(self::D_HTML_REPLACE, $types);
        $m = self::discoveryRules(self::D_HTML_MATCH, $types);

        $k = array();
        $k = array_merge($k, array_keys($r));
        $k = array_merge($k, array_keys($m));

        $k = array_unique($k);

        $results = array();

        foreach($k as $i)
        {
            $a = $html;
            $b = array_key_exists($i, $r) ? $r[$i] : array();
            $c = array_key_exists($i, $m) ? $m[$i] : array();
            $n = count($c);

            if(L_Utility::preg($a, $b, $c) == $n && $n > 0)
            {
                $results[] = $i;
            }
        }

        return (count($results) > 0) ? array_unique($results) : $types;
    }

    /**
     * Find discovery rules by name
     *
     * @param   string  $name
     * @param   array   $types      Blog type IDs array
     *
     * @return  array
     */
    protected static function discoveryRules($name, $types=array())
    {
        $sql = "SELECT blog_type_id, value FROM " . 
               self::$table_name . "_discovery WHERE name = ? ";

        if(count($types) > 0)
        {
            $sql.= "AND blog_type_id IN (" . implode(", ", $types) . ") ";
        }

        $sql.= "ORDER BY blog_type_id, blog_type_discovery_id ASC";

        $results = array();

        foreach(self::select($sql, array($name)) as $i)
        {
            $type = $i->blog_type_id;
            $rules = ((array) unserialize($i->value));
            $results[$type] = (array_key_exists($type, $results)) ?
                array_merge($results[$type], $rules) : 
                $rules;
        }

        return $results;
    }

    /* MANAGER */

    /**
     * Check manager HTML
     * 
     * @param   string      $html
     * @param   array       $config      Blog Type configuration
     * @return  array
     */
    public static function managerCheckHTML(&$html, &$config)
    {
        L_Utility::compactHTML($html);

        $r = array();
        $m = array();

        $k = self::C_MANAGER_HTML_REPLACE;
        if(array_key_exists($k, $config)) $r = unserialize($config[$k]);

        $k = self::C_MANAGER_HTML_MATCH;
        if(array_key_exists($k, $config)) $m = unserialize($config[$k]);

        $t = count($m);

        return (L_Utility::preg($html, $r, $m) == $t && $t > 0);
    }

    /* Blog TYPE PLUGIN */

    /**
     * Load Blog Type plugin info (DEPRECATED)
     * 
     * @param   string          $name       Plugin Name
     * @oaram   string          $version    Plugin Version
     * @oaram   string          $url        Base URL
     * @throws  B_Exception
     * @return  array
     */
    protected static function loadPluginInfo($name, $version, $url)
    {
        $path = APPLICATION_PATH . "/library/BlogType";
        $filename = strtolower($name) . ".py";
        $plugin = null;

        if(!file_exists(($plugin = $path . "/" . $filename)))
        {
#            throw new B_Exception(
#                "plugin (" . $plugin . ") does not exist",
#                E_USER_ERROR);
        }

        $registry = B_Registry::singleton();
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
