<?php
/*
HARDWARE.NO EDITORSETTINGS:
vim:set tabstop=4:
vim:set shiftwidth=4:
vim:set smarttab:
vim:set expandtab:
*/

require_once('/home/lib/libDefines.lib.php');
require_once(AETHER_PATH . 'lib/AetherSection.php');
require_once(AETHER_PATH . 'lib/AetherTextResponse.php');

/**
 * 
 * Entry point to all priceguide applicatino sections
 * 
 * Created: 2007-02-02
 * @author Raymond Julin
 * @package aether.sections
 */

class AetherSectionPriceguide extends AetherSection {
    
    /**
     * Return response
     *
     * @access public
     * @return AetherResponse
     */
    public function response() {
        $response = $this->subsection->response();
        if (($response instanceof AetherTextResponse) == false)
            $response = new AetherTextResponse('foo');
        return $response;
    }
}
?>
