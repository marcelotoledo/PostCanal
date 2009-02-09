<?php

/* BLOTOMATE BOOTSTRAP */

/* autoloader setup */

require "../library/AB/Loader.php";
AB_Loader::register();


/* environment setup */

require "../config/environment.php";


/* session */

Zend_Session::start();


/* dispatch */

AB_Dispatcher::singleton()->dispatch();
