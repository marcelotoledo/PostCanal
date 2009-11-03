#!/bin/bash

maincss="main.css"
blklst="blacklist.txt"
yupath="../../library/yuicompressor/yuicompressor.jar"

rm -fv __tmp__*.css
rm -fv $maincss

for file in $(ls -c1 *.css)
do
    if [ "$(grep $file $blklst)" == "" ]
    then
        java -jar $yupath --type css $file -o __tmp__$file
        echo "compressed \`$file'"
    fi
done

touch $maincss
echo "touched \`$maincss'"

for file in $(ls -c1 __tmp__*.css)
do
    cat $file >> $maincss
    echo "" >> $maincss
done
echo "concatenated \`$maincss'"

rm -fv __tmp__*.css
