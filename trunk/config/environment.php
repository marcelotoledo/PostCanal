<?php

/**
 * Environment configuration
 */

/* php */

date_default_timezone_set ("UTC");
error_reporting (E_ALL);


/* path */

define('BASE_PATH', realpath(dirname(__FILE__) . "/../"));
define('BASE_URL', "/");
define('APPLICATION_PATH', BASE_PATH . "/application");
define('LIBRARY_PATH', BASE_PATH . "/library");

set_include_path (LIBRARY_PATH . PATH_SEPARATOR . get_include_path());


$registry = AB_Registry::singleton();


/* debug */

$registry->debug = true; /* show exceptions in browser */


/* database */

$registry->database->driver   = "pgsql";
$registry->database->host     = "localhost";
$registry->database->username = "autoblog";
$registry->database->password = "autoblog";
$registry->database->db       = "autoblog";


/* response headers */

$registry->response->headers = array
(
    AB_Response::STATUS_OK => array
    (
        'Last-Modified' => gmdate("D, d M Y H:i:s") . " GMT",
        'Cache-Control' => "no-store, no-cache, must-revalidate"
    )
);


/* login session */

$registry->session->namespace       = "login";
$registry->session->expiration      = 57600; /* time in seconds */
$registry->session->check->mode     = AB_Controller::SESSION_CHECK_PERSISTENT;
$registry->session->check->class    = "UserProfile";
$registry->session->check->method   = "checkLogin";
$registry->session->check->redirect = BASE_URL;
