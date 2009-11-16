<?php

/**
 * UserBlogTag model class
 * 
 * @category    PostCanal
 * @package     Application Model
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class UserBlogTag extends B_Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected static $table_name = 'model_user_blog_tag';

    /**
     * Table structure
     *
     * @var array
     */
    protected static $table_structure = array (
		'user_blog_tag_id' => array ('type' => 'integer','size' => 0,'required' => false),
		'user_blog_id' => array ('type' => 'integer','size' => 0,'required' => true),
		'name' => array ('type' => 'string','size' => 50,'required' => true),
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
    protected static $primary_key_name = 'user_blog_tag_id';


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
     * Get UserBlogTag by primary key
     *
     * @param   integer $id    Primary key value
     *
     * @return  UserBlogTag|null 
     */
    public static function getByPrimaryKey($id)
    {
        return current(self::select(
            "SELECT * FROM " . self::$table_name . 
            " WHERE " . self::$primary_key_name . " = ?", 
            array($id), PDO::FETCH_CLASS, get_class()));
    }

    // -------------------------------------------------------------------------

    /**
     * Find all tags by user blog (assoc)
     */
    public static function findAssocFromUserBlog($user, $blog)
    {
        $r = self::select(
            "SELECT user_blog_tag_id, name FROM " . self::$table_name . 
            " WHERE user_blog_id = (
                SELECT user_blog_id 
                FROM model_user_blog 
                WHERE user_profile_id=? 
                AND hash=?) 
            ", array($user, $blog), PDO::FETCH_ASSOC);

        $t=count($r);
        $a=array();

        for($j=0;$j<$t;$j++)
        {
            $a[($r[$j]['user_blog_tag_id'])] = $r[$j]['name'];
        }

        return $a;
    }
}
