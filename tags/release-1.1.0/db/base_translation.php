#!/usr/bin/env php
<?php

/**
 * Translation configuration tool
 * 
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

$f = fopen("base_translation.csv", "r");
$g = fopen("base_translation.mysql", "w");

fwrite($g, "DELETE FROM base_translation WHERE 1=1;\n");
$s = "INSERT INTO base_translation VALUES (NULL, '%s', '%s', '%s', \"%s\");\n";

while(!feof($f))
{
    $a = fgetcsv($f, 4, ',', '"');
    if(count($a) == 4) fwrite($g, vsprintf($s, $a));
}

fclose($f);
fclose($g);
