#!/bin/bash

if [ -z $1 ]; then
    echo "Run: $0 <moduleName>"
    exit 1
fi

outputfile=AetherModule$1.php
if [ -z $outputfile ] || [ -e "$outputfile" ]; then
    echo "Outputfile exists, aborting"
    exit 1
fi

user="`getent passwd $LOGNAME | cut -d':' -f5 | cut -d',' -f1`"
class=$1
date=`date +%Y-%m-%d`

    cat <<EOF >$outputfile
<?php // vim:set ts=4 sw=4 et:
require_once("/home/lib/Autoload.php");

/**
 * 
 * $user is a lazy bastard who owes you a beer
 * 
 * Created: $date
 * @author $user
 * @package 
 */

class AetherModule$1 extends AetherModule {
    public function service(\$name) {
        return new AetherTextResponse(__FILE__ . " says: Nothing to see here");
    }
    public function run() {
        return __FILE__ . " says: Nothing to see here";
    }
}
?>
EOF

$EDITOR $outputfile

