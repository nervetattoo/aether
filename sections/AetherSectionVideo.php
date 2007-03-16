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
require_once(AETHER_PATH . 'lib/AetherModuleFactory.php');
require_once(LIB_PATH . 'video/Video.lib.php');

/**
 * 
 * Entry point to all priceguide applicatino sections
 * 
 * Created: 2007-02-02
 * @author Raymond Julin
 * @package aether.sections
 */

class AetherSectionVideo extends AetherSection {
    
    /**
     * Return response
     *
     * @access public
     * @return AetherResponse
     */
    public function response() {
        $config = $this->sl->fetchCustomObject('aetherConfig');

        try {
            $videoId = $config->getUrlVariable('videoId');
        }
        catch (Exception $e) {} // Do nothing, it only means a specific video was not chosen

        $this->sl->saveCustomObject('video', new Video($videoId));

        return new AetherTextResponse($this->renderModules());
    }
}
?>
