<?php

/**
 * Console
 *
 * @category    PostCanal
 * @package     Application
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

error_reporting (E_STRICT | E_ALL);
date_default_timezone_set('America/Sao_Paulo');


/* configure path */

define('BASE_PATH', realpath(dirname(__FILE__) . '/../'));
define('APPLICATION_PATH', BASE_PATH . "/application");
define('LIBRARY_PATH', BASE_PATH . "/library");

set_include_path (LIBRARY_PATH . PATH_SEPARATOR . get_include_path());


/* load base library */

require LIBRARY_PATH . "/base/monolithic.php";


/* register loader */

B_Loader::register();


/* load registry and configuration */

$registry = B_Registry::singleton(BASE_PATH . '/config/environment.xml');
