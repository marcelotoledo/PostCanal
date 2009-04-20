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
define('APPLICATION_PATH', BASE_PATH . "/application");
define('LIBRARY_PATH', BASE_PATH . "/library");

set_include_path (LIBRARY_PATH . PATH_SEPARATOR . get_include_path());

$registry = B_Registry::singleton(BASE_PATH . '/config/environment.xml');

define('BASE_URL', $registry->base()->url);


$registry->request()->object = null;
$registry->response()->object = null;
$registry->session()->object = null;
$registry->translation()->object = null;

$registry->response()->headers = array
(
    B_Response::STATUS_OK => array
    (
        'Cache-Control' => "no-store, no-cache, must-revalidate",
        'Last-Modified' => gmdate("D, d M Y H:i:s") . " GMT",
    )
);

$registry->session()->unauthorized()->redirect = BASE_URL;
