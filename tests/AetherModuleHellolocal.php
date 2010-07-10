<?php
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
    public function run() {
        return 'Hello local';
    }
}
