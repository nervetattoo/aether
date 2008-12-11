#!/usr/bin/php
<?php // vim:set ts=4 sw=4 et:

/**
 * 
 * Install Aether framework onto computer
 * 
 * Created: 2008-12-11
 * @author Raymond Julin
 */

echo "This will install the Aether PHP framework on this computer\n";
echo "Type install path or default [/home/aether] will be used:\n";
if (!empty($argv[1]) AND $argv[1] != '--help' AND $argv[1] != '-h')
    $path = $argv[1];
else
    exit(printHelp());

$arg = $argv[1];
// See if install or update is wanted
if (preg_match('/(--prefix|-p)=\/[a-z\/]+/', $arg)) 
    install($arg);
elseif (preg_match('/--update|-u/', $arg))
    update();
else
    exit(printHelp());



/**
 * Install Aether using path found in $arg
 *
 * @return void
 */
function install($arg) {
    if (preg_match('/^--prefix|-p=(\/{1}[a-z-_\/]+[a-z-_]+)$/', $arg, $m)) {
        $path = $m[1];
        echo "Trying to install to {$path}/aether\n";
        // Path must exist(?)
        // Check permissions
        if (file_exists($path) AND is_writable($path)) {
            $user = $_SERVER['USER'];
            $here = getcwd();
            // Create aether folder in target path
            // Copy over aether
            $installTo = $path . "/aether/";
            $cmd = 'rsync -vrz --filter=". install_list" . ' . $installTo;
            $result = shell_exec($cmd);
            // Aight, success etc, lets symlink up the "aether" script
            // TODO Fix
            //echo $here."\n";
        }
        else {
            exit("Missing write access to [$path] or it doesnt exist.\n");
        }
    }
    else {
        // Invalid path
        exit("Invalid path supplied\n");
    }
}

/**
 * Update Aether onto preinstall path using updated package at hand
 *
 * @return void
 */
function update() {
}

function printHelp() {
    echo "Installer for the Aether PHP framework\n";
    echo "\t--prefix|-p=/install/path\tWhat path to install in\n";
    echo "\t--update OR -u\tUpdate existing install with new version\n";
    echo "\t--help OR -h\tDisplay this menu\n";
}
?>
