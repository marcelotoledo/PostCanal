<?php

/* AUTOBLOG BOOTSTRAP */

/* autoloader setup */

require "../library/AB/Loader.php";
AB_Loader::register();


/* configuration setup */

require "../config/environment.php";


/* dispatch */

AB_Dispatcher::singleton()->dispatch();
