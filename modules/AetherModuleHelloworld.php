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
 * Simple helloworld module, just a test module
 * 
 * Created: 2007-02-06
 * @author Raymond Julin
 * @package aether.module
 */

class AetherModuleAd200x300 extends AetherModule {
    
    /**
     * Render module
     *
     * @access public
     * @return string
     */
    public function render() {
        $config = $this->sl->fetchCustomObject('aetherConfig');
        $tpl = $this->sl->getTemplate(106);
        $tpl->selectTemplate('gamerAd200x300');

        // Render output and return
        return $tpl->returnPage();
    }
}
?>
