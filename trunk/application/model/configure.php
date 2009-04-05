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


/* ROUTINES */

function pgsqlIntrospection($_table)
{
    $_structure = null;

    $sql = <<<EOS
SELECT
    attrs.attname as attribute,
    "type",
    attrs.attnotnull as nn,
    "default"
FROM (
    SELECT c.oid, n.nspname, c.relname
    FROM pg_catalog.pg_class c 
    LEFT JOIN pg_catalog.pg_namespace n ON n.oid = c.relnamespace
    WHERE pg_catalog.pg_table_is_visible(c.oid)) rel 
JOIN (
    SELECT 
        a.attname, 
        a.attrelid, 
        pg_catalog.format_type(a.atttypid, a.atttypmod) AS "type",
    (SELECT substring(d.adsrc for 128) FROM pg_catalog.pg_attrdef d 
        WHERE d.adrelid = a.attrelid AND d.adnum = a.attnum AND a.atthasdef) as "default",
        a.attnotnull, 
        a.attnum
    FROM pg_catalog.pg_attribute a WHERE a.attnum > 0 AND NOT a.attisdropped) attrs 
    ON (attrs.attrelid = rel.oid )
    WHERE relname = '<table>' ORDER BY attrs.attnum;
EOS;

    /* known types */

    $type_b = "boolean";
    $type_s = "varbit|varchar|text|(bit|character)(varying)*";
    $type_i = "integer|(small|big)*(int|serial)[248]*";
    $type_f = "real|double|float[48]*";
    $type_d = "date|timestamp";

    /* table structure */

    $structure = array();

    /* iterate over fields */

    foreach(B_Model::select(str_replace("<table>", $_table, $sql)) as $r)
    {
        $f = array();

        if    (preg_match("/(" . $type_b . ")+/i", $r->type) > 0) 
            $k = B_Model::TYPE_BOOLEAN;
        elseif(preg_match("/(" . $type_d . ")+/i", $r->type) > 0) 
            $k = B_Model::TYPE_DATE;
        elseif(preg_match("/(" . $type_f . ")+/i", $r->type) > 0) 
            $k = B_Model::TYPE_FLOAT;
        elseif(preg_match("/(" . $type_i . ")+/i", $r->type) > 0) 
            $k = B_Model::TYPE_INTEGER;
        else                                                      
            $k = B_Model::TYPE_STRING;

        /* field type */

        $f[B_Model::STRUCTURE_TYPE] = $k;

        /* field size (only for string); 0 = inf */

        $f[B_Model::STRUCTURE_SIZE] = ($k == B_Model::TYPE_STRING) ? 
            ((int) preg_replace("/^.+\(([0-9]+)\)+.*$/", "\\1", $r->type)) : 
            0;

        $f[B_Model::STRUCTURE_REQUIRED] = ($r->nn && strlen($r->default) == 0);

        $structure[$r->attribute] = $f;
    }

    $_structure = var_export($structure, true);
    $_structure = preg_replace("/[[:space:]]+/", "", $_structure);
    $_structure = preg_replace("/,\)/", ")", $_structure);

    return $_structure;
}

function mysqlIntrospection($_table) /* TODO */
{
    return null;
}


/* MAIN */

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

if(file_exists($_class . ".php"))
{
    $suffix = date("ymd") . "_";
    $suffix.= dechex(substr(number_format(microtime(true), 2, '', ''), -9));

    rename($_class . ".php", $_class . ".php-" . $suffix);

    echo "file \"" . $_class . ".php\" has been renamed to ";
    echo "\"" . $_class . ".php-" . $suffix . "\"\n";
}


/* structure introspection */

$driver = $registry->database->default->driver;

if($driver == "pgsql")
{
    $_structure = pgsqlIntrospection($_table);
}
elseif($driver == "mysql")
{
    $_structure = mysqlIntrospection($_table);
}


/* output */

$output = <<<EOS
<?php

/**
 * <class> model class
 * 
 * @category    Blotomate
 * @package     Model
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
     * Find <class> by primary key
     *
     * @param   integer \$id    Primary key value
     *
     * @return  <class>|null 
     */
    public static function findByPrimaryKey(\$id)
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
