#!/usr/bin/env php
<?php

require "../application/console.php";

$mailer = new L_Mailer();
$mailer->setSubject('test subject');
$mailer->setBody('test body');

try
{
    $mailer->send('rafael@castilho.biz', 'myTestIdentifier');
    echo "mail sent!\n";
    exit(0);
}
catch(Exception $e)
{
    echo $e->getMessage() . "\n";
    exit(1);
}
