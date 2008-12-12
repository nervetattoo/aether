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
    require_once(LIB_PATH . 'AetherAutoload.php');
    
    $loader = new AetherAutoload($name, LIB_PATH, '../lib/');
    $filePath = $loader->load();

    // Require the file
    if (!empty($filePath))
        require_once($filePath);
}

try {
    $aether = new Aether();
    $aether->render();
} 
catch (Exception $e) {
    trigger_error("Uncaught error: " . $e);
}
?>
