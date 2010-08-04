#!/bin/bash
#
# Script for removing AetherModule-prefix from Aether modules 
# created when prefix was needed.  Renames class and filename AetherModuleFoo -> Foo
# Should check your project afterwards since it will rename all classes starting 
# with AetherModule
#
# Recursivly goes through subdirectories from where you run it while searching 
# for files named AetherModule*.php
#
# Example: 
#   simeng@foo:/home/simeng/myproject$ ~/aether/bin/update.sh
#

SCRIPT=$0
ACTION=$1
FILE=$2

if [ -z $ACTION ]; then
    find . -name 'AetherModule*.php' -execdir $SCRIPT rename "{}" \;
else
    sed 's/AetherModule\(\w\)/\1/g' < "$FILE" > "${FILE}.tmp"
    if [ $? -eq 0 ]; then
        mv "${FILE}.tmp" "${FILE/AetherModule/}"
    fi
fi

