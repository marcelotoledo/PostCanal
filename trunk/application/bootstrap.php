<?php

/**
 * Bootstrap
 *
 * @category    PostCanal
 * @package     Application
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

error_reporting (E_ALL);
date_default_timezone_set('UTC');

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


/* load registry and configuration */

$registry = B_Registry::singleton(BASE_PATH . '/config/environment.xml');

define('BASE_URL', $registry->base()->url);
define('VIEW_COMPRESSION', false);


/* configure and run bootstrap */

$bootstrap = new B_Bootstrap();
$bootstrap->run();
