<?php

/**
 * Bootstrap
 *
 * @category    PostCanal
 * @package     Application
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

error_reporting (E_STRICT | E_ALL);


/* check dependencies which are not included 
   in the default installation of debian/php5 */

if(function_exists('curl_version')===false)
{
    echo 'FATAL: Client URL Library (cURL) not found!';
    exit(1);
}


/* configure timezone */

date_default_timezone_set('America/Sao_Paulo');


/* configure path */

define('BASE_PATH', realpath(dirname(__FILE__) . '/../'));
define('APPLICATION_PATH', BASE_PATH . "/application");
define('LIBRARY_PATH', BASE_PATH . "/library");

set_include_path (LIBRARY_PATH . PATH_SEPARATOR . get_include_path());


/* load base library */

require LIBRARY_PATH . "/base/monolithic.php";


/* register loader and session */

B_Loader::register();
B_Session::register();


/* initialize registry and configuration */

B_Registry::load(BASE_PATH . '/config/environment.xml');

define('BASE_URL', B_Registry::get('base/url'));


/* disable Zend Framework Cache in Zend Locale */

Zend_Locale::disableCache(true);


/* configure and run bootstrap */

$bootstrap = new B_Bootstrap();
$bootstrap->run();
