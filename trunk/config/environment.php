<?php

/**
 * Environment configuration
 */

/* php */

date_default_timezone_set ("UTC");
error_reporting (E_ALL);


/* path */

define('BASE_PATH', "/var/www/autoblog");
define('BASE_URL', "http://localhost:8001");
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
$registry->database->username = "autoblog";
$registry->database->password = "autoblog";
$registry->database->db = "autoblog";


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

function new_Zend_Mail() { return new Zend_Mail("UTF-8"); }

$server = "smtp.gmail.com";

$config = array('auth'     => "login",
                'username' => "cdz0vfk61y@gmail.com",
                'password' => "fdm0juk2gn",
                'ssl'      => "ssl",
                'port'     => 465);

$registry->mailer->transport = new Zend_Mail_Transport_Smtp($server, $config);
$registry->mailer->sender->interval->minimum = 86400;
$registry->mailer->sender->from->name = "Autoblog";
$registry->mailer->sender->from->email = "cdz0vfk61y@gmail.com";
