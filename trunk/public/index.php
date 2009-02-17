<?php

/* BLOTOMATE BOOTSTRAP */

/* autoloader setup */

require "../library/AB/Loader.php";
AB_Loader::register();


/* environment setup */

require "../config/environment.php";


/* dispatch */

AB_Dispatcher::singleton()->dispatch();
