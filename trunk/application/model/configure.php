#!/usr/bin/php
<?php

/**
 * Model configuration tool
 */

$_class = $argv[1];
$_table = $argv[2];
$_pk    = $argv[3];


if(empty($_class) ||
   empty($_table) ||
   empty($_pk))
{
    echo "usage: ./configure.php class table pk\n";
    echo "example: ./configure.php UserProfile user_profile user_profile_id\n";

    exit(1);
}

if(file_exists($_class . ".php"))
{
    echo "file \"" . $_class . ".php\" already exists\n";
    echo "remove this file before generating a new\n";

    exit(1);
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
}
EOS;


$output = str_replace ("<class>", $_class, $output);
$output = str_replace ("<table>", $_table, $output);
$output = str_replace ("<pk>",    $_pk,    $output);

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
