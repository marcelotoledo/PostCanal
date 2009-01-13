#!/usr/bin/php
<?php

/**
 * Main configuration tool
 */

$_action = $argv[1];

switch($_action)
{
    case 'setup' :
    break;
    case 'db' :
    break;
    case 'model' :
    break;
    case 'controller' :
    break;
    
    default :
        echo "usage: ./configure.php setup|db|model|controller\n";

        exit(1);
    break;
}

echo "that's all\n";
exit(0);
