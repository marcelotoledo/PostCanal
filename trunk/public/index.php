<?php

/* AUTOBLOG BOOTSTRAP */

/* autoloader setup */

require "../library/AB/Loader.php";
AB_Loader::register();

// require "Zend/Loader.php";
// Zend_Loader::registerAutoload();


/* configuration setup */

require "../config/environment.php";


/* application setup */

// TODO


/* dispatch */

AB_Dispatcher::singleton()->dispatch();
