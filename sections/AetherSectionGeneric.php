<?php
/*
HARDWARE.NO EDITORSETTINGS:
vim:set tabstop=4:
vim:set shiftwidth=4:
vim:set smarttab:
vim:set expandtab:
*/

require_once('/home/lib/libDefines.lib.php');

/**
 * 
 * Generic section, only serves up modules and does nothing.
 * Should mainly be used for prototyping and testing
 * but also totaly generic services like login and logout
 * can use this, unless the context requires some sort of logging
 * 
 * Created: 2007-04-23
 * @author Raymond Julin
 * @package aether.sections
 */

class AetherSectionGeneric extends AetherSection {
    
    /**
     * Return response
     *
     * @access public
     * @return AetherResponse
     */
    public function response() {
        return new AetherTextResponse($this->renderModules());
    }
}
?>
