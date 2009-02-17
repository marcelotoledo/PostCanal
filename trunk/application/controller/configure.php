#!/usr/bin/php
<?php

/**
 * Controller configuration tool
 *
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

$_controller = ucfirst($argv[1]);


if(empty($_controller))
{
    echo "usage: ./configure.php controller\n";
    echo "example: ./configure.php UserProfile\n";

    exit(1);
}

if(file_exists($_controller . "Controller.php"))
{
    echo "file \"" . $_controller . "Controller.php\" already exists\n";
    echo "remove this file before generating a new\n";

    exit(1);
}


$output = <<<EOS
<?php

/**
 * <controller> controller class
 * 
 * @category    Blotomate
 * @package     Controller
 */
class <controller>Controller extends AB_Controller
{
    /**
     * <controller> controller constructor
     *
     * @param   AB_Request  \$request
     * @param   AB_Response \$response
     * @return  void
     */
    public function __construct(\$request, \$response)
    {
        parent::__construct(\$request, \$response);
    }

    /**
     * Default action
     *
     * @return void
     */
    public function indexAction()
    {
    }
}
EOS;


$output = str_replace ("<controller>", $_controller, $output);

try
{
    $f = fopen ($_controller . "Controller.php", "w");
    fwrite($f, $output);
    fclose ($f);
}
catch(Exception $exception)
{
    echo "failed to configure " . $_controller . "\n";
    echo $exception->getMessage() . "\n";

    exit(1);
}

$template_dir = "../view/template/" . $_controller;

if(file_exists($template_dir) == false)
{
    try
    {
        mkdir($template_dir);
        touch($template_dir . "/index.php");
    }
    catch(Exception $exception)
    {
        echo "failed to create templates for " . $_controller . "\n";
        echo $exception->getMessage() . "\n";

        exit(1);
    }
}

echo "successfully configured " . $_controller . "\n";
exit(0);
