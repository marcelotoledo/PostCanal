<?php

require "../application/console.php";

$profile = UserProfile::findByPrimaryKey(1);

print_r($profile);
