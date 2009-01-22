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
 * @category    Autoblog
 * @package     Model
 */
class <class> extends AB_Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected \$table_name = '<table>';

    /**
     * Sequence name
     *
     * @var string
     */
    protected \$sequence_name = <sequence>;

    /**
     * Primary key column name
     *
     * @var string
     */
    protected \$primary_key = '<pk>';


    /**
     * Find <class> with an encapsulated SELECT command
     *
     * @param   array   $conditions WHERE parameters
     * @param   array   $order      ORDER parameters
     * @param   integer $limit      LIMIT parameter
     * @param   integer $offset     OFFSET parameter
     * @return  array
     */
    public static function find (\$conditions=array(), 
                                 \$order=array(), 
                                 \$limit=0, 
                                 \$offset=0)
    {
        \$class_name = get_class();
        \$class_object = new \$class_name();

        return \$class_object->_find(\$conditions, \$order, \$limit, \$offset);
    }

    /**
     * Get <class> with SQL
     *
     * @param   string  $sql    SQL query
     * @param   array   $data   values array
     * @return  array
     */
    public static function selectModel (\$sql, \$data=array())
    {
        \$class_name = get_class();
        \$class_object = new \$class_name();

        return \$class_object->_selectModel(\$sql, \$data);
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
        \$class_name = get_class();
        \$class_object = new \$class_name();

        return \$class_object->_insert(\$sql, \$data);
    }
}
EOS;


$_sequence = empty($_sequence) ? "null" : "'" . $_sequence . "'";

$output = str_replace ("<class>",    $_class,    $output);
$output = str_replace ("<table>",    $_table,    $output);
$output = str_replace ("<sequence>", $_sequence, $output);
$output = str_replace ("<pk>",       $_pk,       $output);


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
