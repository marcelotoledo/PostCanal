#!/bin/bash
total=0
changelog=$@
days_worked=`grep -h HORAS $@ | wc -l | sed -e 's/[ ]*//g'`
value_hour="14.5"

for i in `grep -h HORAS $changelog | sed -e 's/HORAS TRABALHADAS: //g' | sed -e 's/h//g'`; do
    total=$(expr $i + $total); 
done

printf "Days worked:  %d\n" $days_worked
printf "Hours worked: %d\n" $total
printf "Avg hr p/day: %d\n" `expr $total / $days_worked`
printf "Total:        %.2f\n" `echo $total \* $value_hour | bc`
