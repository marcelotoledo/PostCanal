<?php

/**
 * Environment configuration
 */

/* php */

date_default_timezone_set ("UTC");
error_reporting (E_ALL);


/* path */

define('BASE_PATH', realpath(dirname(__FILE__) . "/../"));
define('APPLICATION_PATH', BASE_PATH . "/application");
define('LIBRARY_PATH', BASE_PATH . "/library");

set_include_path (LIBRARY_PATH . PATH_SEPARATOR . get_include_path());


$registry = AB_Registry::singleton();


/* debug (show exceptions) */

$registry->debug = true;


/* database */

$registry->database->driver   = "pgsql";
$registry->database->host     = "localhost";
$registry->database->username = "autoblog";
$registry->database->password = "autoblog";
$registry->database->db       = "autoblog";


/* response headers */

$registry->response->headers = array
(
    200 => array
    (
        'Last-Modified' => gmdate("D, d M Y H:i:s") . " GMT",
        'Cache-Control' => "no-store, no-cache, must-revalidate"
    )
);
