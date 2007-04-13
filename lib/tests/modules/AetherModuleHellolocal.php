<?php
/*
HARDWARE.NO EDITORSETTINGS:
vim:set tabstop=4:
vim:set shiftwidth=4:
vim:set smarttab:
vim:set expandtab:
*/

require_once('/home/lib/libDefines.lib.php');
require_once(AETHER_PATH . 'lib/AetherModule.php');

/**
 * 
 * Simple hellolocal module, just a test module
 * 
 * Created: 2007-02-06
 * @author Raymond Julin
 * @package aether.module
 */

class AetherModuleHellolocal extends AetherModule {
    
    /**
     * Render module
     *
     * @access public
     * @return string
     */
    public function render() {
        return 'Hello local';
    }
}
?>
