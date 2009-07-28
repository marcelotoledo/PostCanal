#!/usr/bin/env php
<?php

require "../application/console.php";


$ws = new L_WebService();
$token = B_Registry::get('webservice/token');

echo "* feed_update_get\n";
$r = $ws->feed_update_get(array('token' => $token));
print_r($r);
echo "\n\n";

echo "* feed_update_post\n";
print_r($ws->feed_update_post(array('token' => $token, 'id' => @$r['id'], 'data' => array())));
echo "\n\n";

echo "* blog_publish_get\n";
$r = $ws->blog_publish_get(array('token' => $token));
print_r($r);
echo "\n\n";

echo "* blog_publish_post\n";
print_r($ws->blog_publish_set(array('token' => $token, 'id' => @$r['id'], 'published' => false)));
echo "\n\n";

echo "* queue_suggest_do\n";
print_r($ws->queue_suggest_do(array('token' => $token)));
echo "\n\n";
