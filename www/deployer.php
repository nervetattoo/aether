<?php
/* vim:set tabstop=4: vim:set shiftwidth=4: vim:set smarttab: vim:set expandtab: */

require_once('/home/lib/libDefines.lib.php');
require(AETHER_PATH . 'Aether.php');

/**
 * 
 * A default deployer for web
 * Usually set as the handler in the webserver.
 * 
 * Created: 2007-01-31
 * @author Raymond Julin
 * @package
 */

/**
 * Tries to find class/interfaces in commonlibs 
 * TODO: Make it look for local libs
 * @param mixed $name Needs to be in CamelCase format
 * @access protected
 * @return void
 */
function __autoload($name) {
    $matches = array();
    $dir = LIB_PATH;

    // Loop over all parts of the class/interface name and find all the parts
    // of it that refers to folder or file names in /home/lib
    $matches = preg_split('/([A-Z][^A-Z]+)/', $name, -1, PREG_SPLIT_NO_EMPTY |
        PREG_SPLIT_DELIM_CAPTURE);

    if (!empty($matches)) {
        $i = 0;
        foreach ($matches as $match) {
            // Check if there is a file with this name. Files have precendence
            // over folders
            $filenameArray = array_slice($matches, $i);

            // Turn the rest of the array into a string that can be used as a filename
            $filename = '';
            foreach ($filenameArray as $fn)
                $filename = $filename . $fn;

            // Break out of the loop if we found the file
            $filename = $dir . $filename . '.php';
            if (file_exists($filename)) {
                $filePath = $filename;
                break;
            }

            $match = strtolower($match);

            // If there is a directory with this name add it to the dir path
            if (file_exists($dir . $match))
                $dir = $dir . $match . "/";
            else
                break;

            $i++;
        }

        // Require the file
        if (!empty($filePath))
            require_once($filePath);
    }
}

try {
    $aether = new Aether();
    $aether->render();
} 
catch (Exception $e) {
    header("Content-Type: text/plain");
    print $e;
}
?>
