<?php

/**
 * Environment configuration
 *
 * @author      Rafael Castilho <rafael@castilho.biz>
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


/* load configuration from xml */

$xml = new Zend_Config_Xml(BASE_PATH . '/config/environment.xml');


/* start registry */

$registry = AB_Registry::singleton();


/* FRAMEWORK */

/* database */

$registry->database = $xml->database;


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

/* model */

$registry->application->utility->encodebase = 'KDqFXGT6HCN9ZbS3R4mAxJ8EckLVPtMz7BQr';


/* login session */

$registry->session->namespace = "login";
$registry->session->expiration = 43200;
$registry->session->unauthorized->redirect = BASE_URL;


/* mailer */

$registry->application->mailer = $xml->mailer;


/* http client */

$registry->application->httpclient->maxheaders = 30;
$registry->application->httpclient->maxbodylenght = 5242880; // 5242880 bytes = 5Mb


/* python */

$registry->python->interpreter->path = "/usr/local/bin/python";
