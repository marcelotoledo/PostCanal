#!/bin/bash
total=0
changelog=$@

for i in `grep HORAS $changelog | sed -e 's/HORAS TRABALHADAS: //g' | sed -e 's/h//g'`; do
    total=$(expr $i + $total); 
done

echo $total
