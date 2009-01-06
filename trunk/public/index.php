<?php

/* AUTOBLOG BOOTSTRAP */

/* configuration setup */

require_once realpath(dirname(__FILE__)) . "/../config/environment.php";


/* autoloader setup */

require_once "AB/Loader.php";
AB_Loader::register();

// require_once "Zend/Loader.php";
// Zend_Loader::registerAutoload();


/* application setup */

// TODO


/* dispatch */

AB_Dispatcher::dispatch();
