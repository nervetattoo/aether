<?php // vim:set ts=4 sw=4 et:

require_once('/home/lib/libDefines.lib.php');
require_once(AETHER_PATH . 'lib/AetherSection.php');

/**
 * 
 * Empty shell of a section to use in test case
 * TODO Use mock instead. Requires changes to section factory aswell
 * 
 * Created: 2009-02-17
 * @author Raymond Julin
 * @package aether.test
 */

class AetherSectionTestsection extends AetherSection {
    
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
