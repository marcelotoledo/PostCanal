<?php

/**
 * BlogType model class
 * 
 * @category    Blotomate
 * @package     Application Model
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class BlogType extends B_Model
{
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
		'blog_type_id' => array ('type' => 'integer','size' => 0),
		'type_name' => array ('type' => 'string','size' => 50,'required' => true),
		'type_label' => array ('type' => 'string','size' => 50,'required' => true),
		'version_name' => array ('type' => 'string','size' => 50,'required' => true),
		'version_label' => array ('type' => 'string','size' => 50,'required' => true),
		'current_revision' => array ('type' => 'integer','size' => 0,'required' => false),
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

    /**
     * Find BlogType by name
     *
     * @param   string  $type_name    Type name
     * @param   integer $version_name Version name
     *
     * @return  BlogType|null 
     */
    public static function findByName($type_name, $version_name)
    {
        return current(self::find(array(
            'type_name' => $type_name,
            'version_name' => $version_name)));
        return current(self::find(array(self::$primary_key_name => $id)));
    }

    /**
     * Discover blog type from URL
     *  
     * @param   string  $url 
     * @return  array
     */ 
    public static function discover($url)
    {
        $client = new A_WebService();
        $args = array('url' => $url);
        $result = $client->blog_discover($args);
        $result['type_accepted'] = false;
        $result['maintenance'] = false;

        if(strlen($result['type']) > 0 && strlen($result['version']) > 0)
        {
            if(is_object(($blog_type = BlogType::findByName($result['type'], 
                                                            $result['version']))))
            {
                $result['type_label'] = $blog_type->type_label;
                $result['version_label'] = $blog_type->version_label;
                $result['type_accepted'] = true;
                $result['maintenance'] = $blog_type->maintenance;
            }
        }

        return $result;
    }

    /**
     * Check URL admin
     *  
     * @param   string  $url 
     * @return  array
     */ 
    public static function checkAdmin($url, $blog_type, $blog_version)
    {
        $client = new A_WebService();
        $args = array('url' => $url, 'type' => $blog_type, 'version' => $blog_version);
        return $client->blog_manager_url_check($args);
    } 
}
