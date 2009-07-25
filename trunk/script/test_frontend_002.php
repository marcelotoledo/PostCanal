#!/usr/bin/env php
<?php

require "../application/console.php";

$u = UserProfile::getByPrimaryKey(1);

echo $u->login_email . "\t" . $u->name . "\n";

$u->login_email = 'rafael+' . mt_rand() . '@castilho.biz';
$u->name = 'Rafael C\'ast"ilho (' . mt_rand() . ')';

$u->save(array('login_email', 'name'));

echo $u->login_email . "\t" . $u->name . "\n";

#var_dump($x);
