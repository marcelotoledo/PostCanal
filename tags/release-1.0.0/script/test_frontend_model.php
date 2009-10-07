#!/usr/bin/env php
<?php

require "../application/console.php";

print_r(UserProfile::getByPrimaryKey(1));
