<?php

/**
 * Bootstrap
 *
 * @category    Blotomate
 * @package     Public
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

/* autoloader setup */

require "../library/base/Loader.php";
B_Loader::register();


/* environment setup */

require "../config/environment.php";


/* session setup */

B_Session::register();


/* dispatch */

B_Main::run();
