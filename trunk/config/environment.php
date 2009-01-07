<?php

/* AUTOBLOG CONFIGURATION */

/* php */

date_default_timezone_set ("UTC");
error_reporting (E_ALL);


/* path */

define('BASE_PATH', realpath(dirname(__FILE__) . "/../"));
define('APPLICATION_PATH', BASE_PATH . "/application");
define('LIBRARY_PATH', BASE_PATH . "/library");

set_include_path (LIBRARY_PATH . PATH_SEPARATOR . get_include_path());


$registry = AB_Registry::singleton();


/* database */

$registry->database_driver   = "pgsql";
$registry->database_host     = "localhost";
$registry->database_username = "autoblog";
$registry->database_password = "autoblog";
$registry->database_db       = "autoblog";
