<?php

/**
 * UserBlogFeedTag model class
 * 
 * @category    PostCanal
 * @package     Application Model
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class UserBlogFeedTag extends B_Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected static $table_name = 'model_user_blog_feed_tag';

    /**
     * Table structure
     *
     * @var array
     */
    protected static $table_structure = array (
		'user_blog_feed_id' => array ('type' => 'integer','size' => 0,'required' => true),
		'user_blog_tag_id' => array ('type' => 'integer','size' => 0,'required' => true));

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
    protected static $primary_key_name = null;


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
     * Get UserBlogFeedTag by primary key
     *
     * @param   integer $id    Primary key value
     *
     * @return  UserBlogFeedTag|null 
     */
    public static function getByPrimaryKey($id)
    {
        return null;
    }

    // -------------------------------------------------------------------------

    /**
     * Find CSV tag list by feed and blog
     */
    public static function findCSVFeedTags($user, $blog)
    {
        $r = self::select(
            'SELECT b.hash AS feed, 
                    c.name AS tag 
             FROM model_user_blog_feed_tag AS a 
             LEFT JOIN model_user_blog_feed AS b ON (a.user_blog_feed_id = b.user_blog_feed_id) 
             LEFT JOIN model_user_blog_tag AS c ON (a.user_blog_tag_id = c.user_blog_tag_id) 
             WHERE b.user_blog_id = (
                SELECT user_blog_id 
                FROM model_user_blog 
                WHERE user_profile_id = ? AND hash = ?) 
             ORDER BY b.user_blog_feed_id', array($user, $blog), PDO::FETCH_ASSOC);

        $l=count($r);
        $a=array();
        $f=null;
        $t=null;

        for($j=0;$j<$l;$j++)
        {
            $f=$r[$j]['feed'];
            $t=$r[$j]['tag'];
            if(array_key_exists($f, $a)==false) $a[$f]=array();
            $a[$f][]=$t;
        }

        foreach(array_keys($a) as $f) $a[$f] = implode(', ', $a[$f]);

        return $a;
    }

    /**
     * Set tags by feed id and tag id array
     */
    public static function setTagIDArray($feed_id, $tag_id_array)
    {
        self::execute('DELETE FROM ' . self::$table_name . ' 
                       WHERE user_blog_feed_id = ?', array($feed_id));

        $l=count($tag_id_array);
        for($j=0;$j<$l;$j++)
        {
            $o=new self();
            $o->user_blog_feed_id=$feed_id;
            $o->user_blog_tag_id=$tag_id_array[$j];
            $o->save();
        }
    }
}
