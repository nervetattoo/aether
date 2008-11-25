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

try {
    $aether = new Aether();
    $aether->render();
} 
catch (Exception $e) {
    header("Content-Type: text/plain");
    print $e;
}
?>
