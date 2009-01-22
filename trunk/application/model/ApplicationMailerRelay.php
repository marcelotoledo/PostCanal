<?php

/**
 * ApplicationMailerRelay model class
 * 
 * @category    Autoblog
 * @package     Model
 */
class ApplicationMailerRelay extends AB_Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table_name = 'application_mailer_relay';

    /**
     * Sequence name
     *
     * @var string
     */
    protected $sequence_name = 'application_mailer_relay_seq';

    /**
     * Primary key column name
     *
     * @var string
     */
    protected $primary_key = 'application_mailer_relay_id';


    /**
     * Find ApplicationMailerRelay with an encapsulated SELECT command
     *
     * @param   array    WHERE parameters
     * @param   array         ORDER parameters
     * @param   integer       LIMIT parameter
     * @param   integer      OFFSET parameter
     * @return  array
     */
    public static function find ($conditions=array(), 
                                 $order=array(), 
                                 $limit=0, 
                                 $offset=0)
    {
        $class_name = get_class();
        $class_object = new $class_name();

        return $class_object->_find($conditions, $order, $limit, $offset);
    }

    /**
     * Get ApplicationMailerRelay with SQL
     *
     * @param   string      SQL query
     * @param   array      values array
     * @return  array
     */
    public static function selectModel ($sql, $data=array())
    {
        $class_name = get_class();
        $class_object = new $class_name();

        return $class_object->_selectModel($sql, $data);
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
        $class_name = get_class();
        $class_object = new $class_name();

        return $class_object->_insert($sql, $data);
    }

    /**
     * Save model
     *
     * @return  boolean
     */
    public function save()
    {
        if($this->isNew()) $this->created_at = date("Y/m/d H:i:s");

        return parent::save();
    }
}
