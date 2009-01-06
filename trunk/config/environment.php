<?php

/* AUTOBLOG CONFIGURATION */

/* php */

date_default_timezone_set ("UTC");
error_reporting (E_ALL);


/* path */

define ('BASE_PATH', realpath(dirname(__FILE__) . "/../"));
define ('APPLICATION_PATH' , BASE_PATH . "/application");
define ('LIBRARY_PATH', BASE_PATH . "/library");
set_include_path (LIBRARY_PATH . PATH_SEPARATOR . get_include_path());


/* database */

define ('DB_HOST', 'localhost');
define ('DB_USER', 'root');
define ('DB_PASS', 's0m3p455');
define ('DB_DB',   'test');
