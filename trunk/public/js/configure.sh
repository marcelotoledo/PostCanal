#!/bin/bash

mainjs="main.js"
blklst="blacklist.txt"
yupath="../../library/yuicompressor/yuicompressor.jar"

rm -fv __tmp__*.js
rm -fv $mainjs

for file in $(ls -c1 *.js)
do
    if [ "$(grep $file $blklst)" == "" ]
    then
        java -jar $yupath --type js $file -o __tmp__$file
        echo "compressed \`$file'"
    fi
done

touch $mainjs
echo "touched \`$mainjs'"

for file in $(ls -c1 __tmp__*.js)
do
    cat $file >> $mainjs
    echo "" >> $mainjs
done
echo "concatenated \`$mainjs'"

rm -fv __tmp__*.js
