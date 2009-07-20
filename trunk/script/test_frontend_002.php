#!/usr/bin/env php
<?php

require "../application/console.php";

//$x = BlogEntry::getByPrimaryKey(25);
$x = UserProfile::getByPrimaryKey(1);

// $x->publication_status = BlogEntry::STATUS_PUBLISHED;

print_r($x);
