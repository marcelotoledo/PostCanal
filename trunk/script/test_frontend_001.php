#!/usr/bin/env php
<?php

require "../application/console.php";

// print_r(Test::getByPrimaryKey(1));

print_r(Test::find(array('hash'), array('user_profile_id' => 1)));
