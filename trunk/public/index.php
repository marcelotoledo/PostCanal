<?php

/**
 * Bootstrap
 *
 * @category    Blotomate
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

/* autoloader setup */

require "../library/AB/Loader.php";
AB_Loader::register();


/* environment setup */

require "../config/environment.php";


/* session setup */

AB_Session::register();


/* dispatch */

AB_Dispatcher::singleton()->dispatch();
