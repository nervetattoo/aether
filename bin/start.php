#!/usr/bin/php
<?php // vim:set ts=4 sw=4 et:

/**
 * 
 * Generate the basics for a new project based on Aether
 * 
 * Created: 2008-01-28
 * @author Raymond Julin
 */

// Read project name from argc1
if (!empty($argv[1]))
    $name = $argv[1];
else
    exit("You must supply a project name\nExiting\n");

// Determine the folder the script were called for as this is the root of the new project
$root = getcwd();

// Find base folder for aether instance we're running
$aether = preg_replace("/bin\/[a-zA-Z0-9.]*$/", "", __FILE__);

// Create directories
$rootdir = $root . "/" . $name;
mkdir($rootdir);
mkdir($rootdir . "/config");
mkdir($rootdir . "/www");
mkdir($rootdir . "/static");
mkdir($rootdir . "/aether");
mkdir($rootdir . "/modules");
mkdir($rootdir . "/sections");
mkdir($rootdir . "/templates");

// Copy in starter config
copy($aether . "config/aether.config.xml", $rootdir . "/config/aether.config.xml");
copy($aether . "www/deployer.php", $rootdir . "/www/deployer.php");

exit("Project [$name] succesfully created\nExiting\n");
