<?php

/**
 * Environment configuration
 */

/* php */

date_default_timezone_set ("UTC");
error_reporting (E_ALL);


/* path */

define('BASE_PATH', "/var/www/blotomate");
define('BASE_URL', "http://127.0.0.1:8001");
define('APPLICATION_PATH', BASE_PATH . "/application");
define('LIBRARY_PATH', BASE_PATH . "/library");

set_include_path (LIBRARY_PATH . PATH_SEPARATOR . get_include_path());


$registry = AB_Registry::singleton();


/* FRAMEWORK */

/* debug */

$registry->debug = true; /* show exceptions in browser */


/* database */

$registry->database->driver = "pgsql";
$registry->database->host = "localhost";
$registry->database->username = "blotomate";
$registry->database->password = "blotomate";
$registry->database->db = "blotomate";
$registry->database->timezone = "UTC";


/* response headers */

$registry->response->headers = array
(
    AB_Response::STATUS_OK => array
    (
        'Last-Modified' => gmdate("D, d M Y H:i:s") . " GMT",
        'Cache-Control' => "no-store, no-cache, must-revalidate"
    )
);


/* APPLICATION */

/* login session */

$registry->session->namespace = "login";
$registry->session->expiration = 43200;
$registry->session->unauthorized->redirect = BASE_URL;


/* mailer */

$registry->mailer->server = "smtp.gmail.com";
$registry->mailer->auth = "login";
$registry->mailer->ssl = "ssl";
$registry->mailer->port = 465;
$registry->mailer->sender->username = "cdz0vfk61y@gmail.com";
$registry->mailer->sender->password = "fdm0juk2gn";
$registry->mailer->sender->email = "cdz0vfk61y@gmail.com";
$registry->mailer->relay->time = 43200;
$registry->mailer->relay->count = 2;
