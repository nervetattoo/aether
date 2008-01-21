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
require_once(AETHER_PATH . 'lib/AetherActionResponse.php');
require_once(LIB_PATH . 'SessionHandler.lib.php');
require_once(LIB_PATH . 'user/UserFinder.lib.php');
require_once(LIB_PATH . 'user/User.lib.php');
require_once(LIB_PATH . 'user/UserAuthenticator.lib.php');

/**
 * 
 * User login
 * 
 * Created: 2007-02-07
 * @author Raymond Julin
 * @package aether.module
 */

class AetherModuleLogout extends AetherModule {
    
    /**
     * Render module
     *
     * @access public
     * @return string
     */
    public function render() {
        // Log user in if requested
        $session = $this->sl->fetchCustomObject('session');
        $session->close();

        /* This is hackish from a to z. First of all
         * we shouldnt just replace /login as other names
         * could be used. Secondly its wrong to draw() in
         * the actual module, this should happen in the
         * section
         */
        $config = $this->sl->fetchCustomObject('aetherConfig');
        $response = new AetherActionResponse(302, $config->getBase());
        $response->draw();
    }
}
?>
