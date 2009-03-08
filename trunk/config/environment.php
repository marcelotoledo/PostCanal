<?php

/**
 * Environment configuration
 *
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

/* PHP */

date_default_timezone_set ("UTC");
error_reporting (E_ALL);


/* PATH */

define('BASE_PATH', "/var/www/blotomate");
define('BASE_URL', "http://127.0.0.1:8001");
define('APPLICATION_PATH', BASE_PATH . "/application");
define('LIBRARY_PATH', BASE_PATH . "/library");

set_include_path (LIBRARY_PATH . PATH_SEPARATOR . get_include_path());


/* REGISTRY */

$registry = B_Registry::singleton();
$registry->load(BASE_PATH . '/config/environment.xml');

/* BASE */

$registry->request->object = null;
$registry->response->object = null;
$registry->session->object = null;
$registry->translation->object = null;

$registry->response->headers = array
(
    B_Response::STATUS_OK => array
    (
        'Last-Modified' => gmdate("D, d M Y H:i:s") . " GMT",
        'Cache-Control' => "no-store, no-cache, must-revalidate"
    )
);

$registry->session->name = 'blotomate';
$registry->session->expiration = 43200;
$registry->session->unauthorized->redirect = BASE_URL;

$registry->translation->culture = 'us_EN';

/* APPLICATION */

/* mailer */

/* http client */

$registry->application->httpClient->maxHeaders = 30;
$registry->application->httpClient->maxBodyLenght = 5242880; // 5242880 bytes = 5Mb

/* python */

$registry->python->interpreter->path = "/usr/local/bin/python";
