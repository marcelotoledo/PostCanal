<?php

/**
 * UserDashboard model class
 * 
 * @category    PostCanal
 * @package     Application Model
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class UserDashboard extends B_Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected static $table_name = 'model_user_dashboard_setting';

    /**
     * Table structure
     *
     * @var array
     */
    protected static $table_structure = array (
		'user_dashboard_setting_id' => array ('type' => 'integer','size' => 0,'required' => false),
		'user_profile_id' => array ('type' => 'integer','size' => 0,'required' => true),
		'context' => array ('type' => 'string','size' => 100,'required' => true),
		'name' => array ('type' => 'string','size' => 100,'required' => true),
		'value' => array ('type' => 'string','size' => 200,'required' => false));

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
    protected static $primary_key_name = 'user_dashboard_setting_id';


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
     * Execute a SQL insert query and returns last insert id
     *
     * @param   string  $sql        SQL query
     * @param   array   $data       values array
     * @return  integer
     */
    public static function insert($sql, $data=array())
    {
        return parent::insert_($sql, $data, self::$sequence_name);
    }

    /**
     * Get UserDashboard by primary key
     *
     * @param   integer $id    Primary key value
     *
     * @return  UserDashboard|null 
     */
    public static function getByPrimaryKey($id)
    {
        return current(self::select(
            "SELECT * FROM " . self::$table_name . 
            " WHERE " . self::$primary_key_name . " = ?", 
            array($id), PDO::FETCH_CLASS, get_class()));
    }

    // -------------------------------------------------------------------------

    public static $settings_default = array 
    (
        'blog' => array
        (
            'current' => ''
        ),
        'feed' => array
        (
            'display' => 'all'
        ),
        'article' => array
        (
            'display' => 'list'
        ),
        'queue' => array
        (
            'height' => 0,
        )
    );

    /**
     * Get settings from profile
     *
     * @param   integer $id
     * 
     * @return  array
     */
    public static function getByUser($id)
    {
        $data = self::select(
            "SELECT * FROM " . self::$table_name . 
            " WHERE user_profile_id = ? ORDER BY context, name", 
            array($id), PDO::FETCH_OBJ);

        $results = self::$settings_default;

        foreach($data as $d)
        {
            $results[$d->context][$d->name] = $d->value;
        }

        return L_Utility::array2Object($results);
    }

    /**
     * Save setting
     *
     * @param   integer $id         User Profile ID
     * @param   string  $context
     * @param   string  $name
     * @param   mixed   $value
     */
    public static function saveSetting($id, $context, $name, $value)
    {
        if(array_key_exists($context, self::$settings_default))
        {
            if(array_key_exists($name, self::$settings_default[$context]))
            {
                $sql = "REPLACE INTO " . self::$table_name .
                       " (user_profile_id, context, name, value) " .
                       " VALUES (?, ?, ?, ?)";
                self::execute($sql, array($id, $context, $name, $value));
            }
        }
    }
}
