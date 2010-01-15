#!/usr/bin/env php
<?php

require "../application/console.php";

$ws = new L_WebService();
$token = B_Registry::get('webservice/token');

echo "* feed_update_get\n";
$r = $ws->feed_update_get(array('token' => $token));
print_r($r);
