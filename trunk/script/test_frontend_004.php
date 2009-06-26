#!/usr/bin/env php
<?php

require "../application/console.php";


print_r(UserBlogFeed::findArticlesToSuggestion(1));
