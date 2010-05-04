<?php
/* vim:set tabstop=4: vim:set shiftwidth=4: vim:set smarttab: vim:set expandtab: */

require('../Aether.php');

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
    trigger_error("Uncaught error: " . $e);
}
?>
