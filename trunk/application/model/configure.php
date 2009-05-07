#!/usr/bin/php
<?php

/**
 * Model configuration tool
 *
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

require "../../library/base/Loader.php";
B_Loader::register();

require "../../config/environment.php";
$registry = B_Registry::singleton();


@$_class     = $argv[1];
@$_table     = $argv[2];
@$_pk        = $argv[3];
@$_sequence  = $argv[4];
@$_structure = null;


if(empty($_class) ||
   empty($_table) ||
   empty($_pk))
{
    echo "usage: ./configure.php class table pk [sequence]\n";
    echo "example: ./configure.php User user user_id [user_seq]\n";

    exit(1);
}

/* backup */

/*
if(file_exists($_class . ".php"))
{
    $suffix = date("ymd") . "_";
    $suffix.= dechex(substr(number_format(microtime(true), 2, '', ''), -9));

    rename($_class . ".php", $_class . ".php-" . $suffix);

    echo "file \"" . $_class . ".php\" has been renamed to ";
    echo "\"" . $_class . ".php-" . $suffix . "\"\n";
}
*/

/* introspection */

$type_b = "tinyint\(1\)";
$type_s = "char|varchar|text";
$type_i = "tinyint|smallint|mediumint|int";
$type_f = "float";
$type_d = "date|timestamp";

$structure = array();

foreach(B_Model::select("EXPLAIN " . $_table) as $r)
{
    $f = array();

    if    (preg_match("/(" . $type_b . ")+/i", $r->Type) > 0) 
        $k = B_Model::TYPE_BOOLEAN;
    elseif(preg_match("/(" . $type_d . ")+/i", $r->Type) > 0) 
        $k = B_Model::TYPE_DATE;
    elseif(preg_match("/(" . $type_f . ")+/i", $r->Type) > 0) 
        $k = B_Model::TYPE_FLOAT;
    elseif(preg_match("/(" . $type_i . ")+/i", $r->Type) > 0) 
        $k = B_Model::TYPE_INTEGER;
    else                                                      
        $k = B_Model::TYPE_STRING;


    $f[B_Model::STRUCTURE_TYPE] = $k;

    /* field size (only for string); 0 = inf */

    $f[B_Model::STRUCTURE_SIZE] = ($k == B_Model::TYPE_STRING) ? 
        ((int) preg_replace("/^.+\(([0-9]+)\)+.*$/", "\\1", $r->Type)) : 0;

    if($r->Field != $_pk)
    {
        $f[B_Model::STRUCTURE_REQUIRED] = ($r->Null == "NO" && strlen($r->Default) == 0);
    }
    else
    {
        $f[B_Model::STRUCTURE_REQUIRED] = false;
    }

    $structure[$r->Field] = $f;
}

$_structure = var_export($structure, true);
$_structure = preg_replace("/,\n/", ",", $_structure);
$_structure = preg_replace("/\(\n/", "(", $_structure);
$_structure = preg_replace("/\),/", "),\n", $_structure);
$_structure = preg_replace("/=>\s+\n\s+/", "=> ", $_structure);
$_structure = preg_replace("/\(\s+/", "(", $_structure);
$_structure = preg_replace("/,\s+\'/", ",'", $_structure);
$_structure = preg_replace("/\),\'/", "),\n\t\t'", $_structure);
$_structure = preg_replace("/,\s+\)/", ")", $_structure);
$_structure = preg_replace("/^array\s+\(/", "array (\n\t\t", $_structure);

/* output */

$output = <<<EOS
<?php

/**
 * <class> model class
 * 
 * @category    Blotomate
 * @package     Application Model
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class <class> extends B_Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected static \$table_name = '<table>';

    /**
     * Table structure
     *
     * @var array
     */
    protected static \$table_structure = <structure>;

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
     * Get table structure
     *
     * @return  array
     */
    public function getTableStructure()
    {
        return self::\$table_structure;
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
        return parent::_insert(\$sql, \$data, self::\$sequence_name);
    }

    /**
     * Get <class> by primary key
     *
     * @param   integer \$id    Primary key value
     *
     * @return  <class>|null 
     */
    public static function getByPrimaryKey(\$id)
    {
        return current(self::find(array(self::\$primary_key_name => \$id)));
    }
}
EOS;


/* replace variables */

$_sequence = empty($_sequence) ? "null" : "'" . $_sequence . "'";
if(empty($_structure)) $_structure = "array()";

$output = str_replace ("<class>",      $_class,     $output);
$output = str_replace ("<table>",      $_table,     $output);
$output = str_replace ("<<sequence>>", $_sequence,  $output);
$output = str_replace ("<pk>",         $_pk,        $output);
$output = str_replace ("<structure>",  $_structure, $output);


/* write model */

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
    echo $exception->getMessage() . "\n";

    exit(1);
}
