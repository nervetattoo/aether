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
        $config = $this->sl->fetchCustomObject('aetherConfig');
        // We have a text response, good, now wrap it and have it processed
        $tpl = $this->sl->getTemplate(99);
        $tpl->selectTemplate('wrapper_prisguide.no');
        $tpl->setVar('urlbase', $config->getBase());
        if ($this->sl->hasCustomObject('user')) {
            $user = $this->sl->fetchCustomObject('user');
            $tplUser = array(
                'id' => $user->get('userId'),
                'username' => $user->get('username'),
                'email' => $user->get('email'));
            $tpl->setVar('user', $tplUser);
        }
        else {
            $tpl->setVar('user', false);
        }

        // Inherited method
        $tpl->setVar('content', $this->renderModules());


        /**
         * Load bread crumbs info
         */
        try {
            $crumbs = (array) $this->sl->fetchCustomObject('breadcrumbs');
            $tpl->setVar('crumbs', $crumbs);
        }
        catch (Exception $e) {
            // And do nothing
        }

        $response = new AetherTextResponse($tpl->returnPage());
        return $response;
    }
}
?>
