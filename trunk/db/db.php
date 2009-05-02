#!/usr/bin/env php
<?php

/**
 * Database configuration tool
 * 
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

require "../application/console.php";


function read_sql ($filename)
{
    $s = (string)(null);

    if(file_exists($filename) && is_readable($filename))
    {
        $f = fopen($filename, "r");
        while(!feof($f)) $s.= fgets($f); 
    }
    else
    {
        throw new Exception ("file " . $filename . " can not be read\n");
    }
    
    return $s;
}

function read_and_run_sql ($filename)
{
    try
    {
        echo "loading " . $filename . " ... ";
        $sql = read_sql ($filename);
        B_Model::execute($sql);
        echo $filename . " loaded\n";
    }
    catch(Exception $exception)
    {
        echo "failed to configure " . $filename . "; " . $exception . "\n";

        exit(1);
    }
}


if ($argc == 1)
{
    echo "usage: ./db.php file1.sql file2.sql ...\n";

    exit(1);
}

for($i=1; $i < $argc; $i++)
{
    read_and_run_sql ($argv[$i]);
}
