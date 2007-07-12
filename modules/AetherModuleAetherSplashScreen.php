<?php // vim:set ts=4 sw=4 et:

/**
 * 
 * Provide a default Aether splash screen that shows when no
 * custom configuraiton is set up
 * 
 * Created: 2007-07-10
 * @author Raymond Julin
 * @package aether.module
 */

class AetherModuleAetherSplashScreen extends AetherModule {
    
    /**
     * REnder module
     *
     * @access public
     * @return string
     */
    public function render() {
        $tpl = $this->sl->getTemplate(98);
        $tpl->selectTemplate('splash');
        return $tpl->returnPage();
    }
}
?>
