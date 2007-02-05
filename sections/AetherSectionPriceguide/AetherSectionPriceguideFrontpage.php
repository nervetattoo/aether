<?php
/*
HARDWARE.NO EDITORSETTINGS:
vim:set tabstop=4:
vim:set shiftwidth=4:
vim:set smarttab:
vim:set expandtab:
*/

require_once('/home/lib/libDefines.lib.php');
require_once(AETHER_PATH . 'lib/AetherSubSection.php');
require_once(AETHER_PATH . 'lib/AetherTextResponse.php');

/**
 * 
 * I_AM_TOO_LAZY_TO_WRITE_A_DESCRIPTION
 * 
 * Created: 2007-02-02
 * @author Raymond Julin
 * @package aether.sections.priceguide
 */

class AetherSectionPriceguideFrontpage extends AetherSubSection {
    
    /**
     * Render and return response
     *
     * @access public
     * @return AetherResponse
     */
    public function response() {
        $response = new AetherTextResponse('frontpage');
        return $response;
    }
}

?>
