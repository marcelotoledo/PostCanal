#!/usr/bin/php
<?php

/**
 * Database configuration tool
 */

require "../library/AB/Loader.php";
AB_Loader::register();

require "../config/environment.php";


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
        AB_Model::execute($sql);
        echo $filename . " loaded\n";
    }
    catch(Exception $exception)
    {
        echo "failed to configure " . $filename . "; " . $exception . "\n";

        exit(1);
    }
}


if ($argc == 0)
{
    echo "usage: ./configure.php file1.sql file2.sql ...\n";

    exit(1);
}

for($i=1; $i < $argc; $i++)
{
    read_and_run_sql ($argv[$i]);
}
