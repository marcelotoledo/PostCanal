#!/bin/bash

for file in $(find ../application/view -type f | \
              grep -v ".svn" | \
              grep -v "cache" | \
              sort)
do
    echo "--------------------------------------------------------------------------------"
    echo "$file"
    echo "--------------------------------------------------------------------------------"

    grep "translation()->" $file | \
    sed s/"^.*translation()->"//g | \
    sed s/"\ .*"//g | \
    sed s/"[^a-zA-Z0-9_]"//g | \
    sort -u
done
