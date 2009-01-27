#!/usr/bin/php
<?php

/**
 * Model configuration tool
 */

$_class    = $argv[1];
$_table    = $argv[2];
$_pk       = $argv[3];
$_sequence = $argv[4];


if(empty($_class) ||
   empty($_table) ||
   empty($_pk))
{
    echo "usage: ./configure.php class table pk [sequence]\n";
    echo "example: ./configure.php User user user_id [user_seq]\n";

    exit(1);
}

if(file_exists($_class . ".php"))
{
    $suffix = date("ymd") . "_";
    $suffix.= dechex(substr(number_format(microtime(true), 2, '', ''), -9));

    rename($_class . ".php", $_class . ".php-" . $suffix);

    echo "file \"" . $_class . ".php\" has been renamed to ";
    echo "\"" . $_class . ".php-" . $suffix . "\"\n";
}


$output = <<<EOS
<?php

/**
 * <class> model class
 * 
 * @category    Blotomate
 * @package     Model
 */
class <class> extends AB_Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected static \$table_name = '<table>';

    /**
     * Sequence name
     *
     * @var string
     */
    protected static \$sequence_name = <<sequence>>;

    /**
     * Primary key name
     *
     * @var string
     */
    protected static \$primary_key_name = '<pk>';


    /**
     * Get table name
     *
     * @return  string
     */
    public function getTableName()
    {
        return self::\$table_name;
    }

    /**
     * Get sequence name
     *
     * @return  string
     */
    public function getSequenceName()
    {
        return self::\$sequence_name;
    }

    /**
     * Get primary key name
     *
     * @return  string
     */
    public function getPrimaryKeyName()
    {
        return self::\$primary_key_name;
    }

    /**
     * Save model
     *
     * @return  boolean
     */
    public function save()
    {
        return parent::_save(self::\$sequence_name);
    }

    /**
     * Find <class> with an encapsulated SELECT command
     *
     * @param   array   \$conditions WHERE parameters
     * @param   array   \$order      ORDER parameters
     * @param   integer \$limit      LIMIT parameter
     * @param   integer \$offset     OFFSET parameter
     * @return  array
     */
    public static function find (\$conditions=array(), 
                                 \$order=array(), 
                                 \$limit=0, 
                                 \$offset=0)
    {
        return parent::_find(\$conditions, 
                             \$order, 
                             \$limit, 
                             \$offset, 
                             self::\$table_name,
                             get_class());
    }

    /**
     * Get <class> with SQL
     *
     * @param   string  \$sql    SQL query
     * @param   array   \$data   values array
     * @return  array
     */
    public static function selectModel (\$sql, \$data=array())
    {
        return parent::_selectModel(\$sql, \$data, get_class());
    }

    /**
     * Execute a SQL insert query and returns last insert id
     *
     * @param   string  \$sql        SQL query
     * @param   array   \$data       values array
     * @return  integer
     */
    public static function insert(\$sql, \$data=array())
    {
        return parent::_insert(\$sql, \$data, self::sequence_name);
    }

    /**
     * Get <class> from primary key
     *
     * @param   integer \$id    Primary key value
     *
     * @return  <class>|null 
     */
    public static function getFromPrimaryKey(\$id)
    {
        return current(self::find(array(self::\$primary_key_name => \$id)));
    }
}
EOS;


$_sequence = empty($_sequence) ? "null" : "'" . $_sequence . "'";

$output = str_replace ("<class>",      $_class,    $output);
$output = str_replace ("<table>",      $_table,    $output);
$output = str_replace ("<<sequence>>", $_sequence, $output);
$output = str_replace ("<pk>",         $_pk,       $output);


try
{
    $f = fopen ($_class . ".php", "w");
    fwrite($f, $output);
    fclose ($f);

    echo "successfully configured " . $_class . "\n";

    exit(0);
}
catch(Exception $exception)
{
    echo "failed to configure " . $_class . "\n";
    echo $exception . "\n";

    exit(1);
}
