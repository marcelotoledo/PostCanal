#!/usr/bin/env sh

echo "<?php" > monolithic.php

for file in $(ls -c1 *.php | grep -v "monolithic.php" | sort)
do
    cat $file | grep -v "<?php" >> monolithic.php
done

php -l monolithic.php
