#!/bin/bash

config='../config/environment.xml'

_pter=$(wc -l $config | sed s/"\s.*"//g)

_tagl=$(cat $config | tail -n $_pter | grep -n "<environment>" | sed s/":.*"//g | head -n 1)
_pter=$(expr $_pter - $_tagl)
_tagl=$(cat $config | tail -n $_pter | grep -n "<database>" | sed s/":.*"//g | head -n 1)
_pter=$(expr $_pter - $_tagl)
_tagl=$(cat $config | tail -n $_pter | grep -n "<default>" | sed s/":.*"//g | head -n 1)
_pter=$(expr $_pter - $_tagl)

_tagv=$(cat $config | tail -n $_pter | grep "<host>" | sed s/":.*"//g | head -n 1)
_tagv=$(echo $_tagv | sed s/"<[^>]\+>"//g)
__H=$_tagv

_tagv=$(cat $config | tail -n $_pter | grep "<username>" | sed s/":.*"//g | head -n 1)
_tagv=$(echo $_tagv | sed s/"<[^>]\+>"//g)
__U=$_tagv

_tagv=$(cat $config | tail -n $_pter | grep "<password>" | sed s/":.*"//g | head -n 1)
_tagv=$(echo $_tagv | sed s/"<[^>]\+>"//g)
__P=$_tagv

_tagv=$(cat $config | tail -n $_pter | grep "<db>" | sed s/":.*"//g | head -n 1)
_tagv=$(echo $_tagv | sed s/"<[^>]\+>"//g)
__D=$_tagv

_fn="$__D-dump-$(date +%s).mysql"

echo "dumping to $_fn"

/usr/bin/env mysqldump -u$__U -p$__P -h$__H $__D > $_fn
