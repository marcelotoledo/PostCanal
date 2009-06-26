#!/bin/bash

find ../application/view -type f | grep -v ".svn" | xargs grep "translation()->" | sed s/"^.*translation()->"//g | sed s/"\ .*"//g | sed s/"[^a-zA-Z0-9_]"//g | sort -u
