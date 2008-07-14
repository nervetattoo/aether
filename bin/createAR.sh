#!/bin/bash

if [ -z $1 ]; then
    echo "Run: $0 <className>"
    exit 1
fi

outputfile=$1.php
if [ -z $outputfile ] || [ -e "$outputfile" ]; then
    echo "Outputfile exists, aborting"
    exit 1
fi

user="`getent passwd $LOGNAME | cut -d':' -f5 | cut -d',' -f1`"
class=$1
date=`date +%Y-%m-%d`

    cat <<EOF >$outputfile
<?php // vim:set ts=4 sw=4 et:

require_once("/home/lib/libDefines.lib.php");
require_once(LIB_PATH . "activerecord/ActiveRecord.php");

/**
 * 
 * $user is a lazy bastard who owes you a beer
 * 
 * Created: $date
 * @author $user
 * @package 
 */


class $class extends ActiveRecord {
    public \$tableInfo = array(
            'database' => 'db',
            'table' => 'table',
            'keys' => array(
                'id' => 'id', 
            ),
            'indexes' => array(
                'id' => 'id'
            ),
            'fields' => array(
                'id' => 'id'
            ),
            'relations' => array()
    );
}

?>
EOF

$EDITOR $outputfile

