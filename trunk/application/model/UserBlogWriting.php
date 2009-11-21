<?php

/**
 * UserBlogWriting model class
 * 
 * @category    PostCanal
 * @package     Application Model
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class UserBlogWriting extends B_Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected static $table_name = 'model_user_blog_writing';

    /**
     * Table structure
     *
     * @var array
     */
    protected static $table_structure = array (
		'user_blog_writing_id' => array ('type' => 'integer','size' => 0,'required' => false),
		'user_blog_id' => array ('type' => 'integer','size' => 0,'required' => true),
		'hash' => array ('type' => 'string','size' => 8,'required' => true),
		'writing_title' => array ('type' => 'string','size' => 0,'required' => true),
		'writing_content' => array ('type' => 'string','size' => 0,'required' => true),
		'created_at' => array ('type' => 'date','size' => 0,'required' => false),
		'updated_at' => array ('type' => 'date','size' => 0,'required' => false),
		'deleted' => array ('type' => 'boolean','size' => 0,'required' => false));

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
    protected static $primary_key_name = 'user_blog_writing_id';


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
     * Get UserBlogWriting by primary key
     *
     * @param   integer $id    Primary key value
     *
     * @return  UserBlogWriting|null 
     */
    public static function getByPrimaryKey($id)
    {
        return current(self::select(
            "SELECT * FROM " . self::$table_name . 
            " WHERE " . self::$primary_key_name . " = ?", 
            array($id), PDO::FETCH_CLASS, get_class()));
    }

    // -------------------------------------------------------------------------

    public function save()
    {
        if($this->isNew())
        {
            $this->hash = L_Utility::randomString(8);
        }

        return parent::save();
    }

    /**
     * get writing
     */
    public static function getWriting($user, $blog, $writing)
    {
        $sql = "SELECT * FROM " . self::$table_name . "
                WHERE hash = ?
                AND user_blog_id = (
                    SELECT user_blog_id
                    FROM model_user_blog
                    WHERE hash = ? AND user_profile_id = ?)";
        return current(self::select($sql, 
            array($writing, $blog, $user), PDO::FETCH_CLASS, get_class()));
    }

    /**
     * find writing for user blog
     */
    public static function findWritings($user, $blog, $older, $limit=25)
    {
        if(!$older) $older = time();

        $sql = "SELECT hash AS writing, writing_title, writing_content
                FROM " . self::$table_name . "
                WHERE deleted = 0 AND created_at < ?
                AND user_blog_id = (
                    SELECT user_blog_id
                    FROM model_user_blog
                    WHERE hash = ? AND user_profile_id = ?)
                ORDER BY created_at DESC
                LIMIT " . intval($limit);

        return self::select($sql, array(date("Y-m-d H:i:s", $older),
                                        $blog, $user), PDO::FETCH_ASSOC);
    }
}
