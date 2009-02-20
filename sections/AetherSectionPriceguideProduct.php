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
 * Entry point for priceguide product page
 * 
 * Created: 2007-02-21
 * @author Raymond Julin
 * @package aether.sections
 */

class AetherSectionPriceguideProduct extends AetherSection {
    
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
        $tpl->setVar('content', $this->renderModules($tpl));

        $response = new AetherTextResponse($tpl->returnPage());
        return $response;
    }
}
?>
