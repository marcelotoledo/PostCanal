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
     * CMSType application library object
     *
     * @var Object
     */
    protected $library_object = null;


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
     * Get CMSType application library object
     *
     * @return  Object
     */
    public function getLibraryObject()
    {
        if($this->isNew())
        {
            $message = "a new object cannot use this method";
            throw new Exception($message);
        }

        if(!is_object($this->library_object))
        {
            $this->library_object = self::loadLibraryClass(
                $this->name, $this->version);
        }

        return $this->library_object;
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

    /**
     * Load CMS Type application library class
     *
     * @param   string          $name
     * @oaram   string          $version
     * @return  Object|null
     */
    protected static function loadLibraryClass($name, $version)
    {
        $path = APPLICATION_PATH . "/library/CMSType";

        if(!file_exists($path))
        {
            $message = "directory " . $path . " not found";
            throw new Exception($message);
        }

        $name = eregi_replace("[^[:alpha:]]", "_", $name);
        $version = eregi_replace("[^[:alpha:]]", "_", $version);

        /* load essential classes */

        self::_loadClass(
            "CMSTypeAbstract", 
            $path . "/Abstract.php");

        self::_loadClass(
            "CMSTypeInterface", 
            $path . "/Interface.php");

        self::_loadClass(
            "CMSType" . $name . "Abstract", 
            $path . "/" . $name . "/Abstract.php");

        self::_loadClass(
            "CMSType" . $name . $version, 
            $path . "/" . $name . "/" . $version . "/Main.php");

        if(!class_exists(($c = "CMSType" . $name . $version)))
        {
            $message = "class ". $c . " not found";
            throw new Exception($message);
        }

        return new $c;
    }


    /**
     * Simple class loader
     *
     * @param   $name   Class name
     * @param   $path   Class path
     * @return  void
     */
    protected static function _loadClass($name, $path)
    {
        if(!class_exists($name))
        {
            if(file_exists($path))
            {
                include $path;
            }
        }
    }
}
