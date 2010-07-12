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

class SplashScreen extends AetherModule {
    
    /**
     * REnder module
     *
     * @access public
     * @return string
     */
    public function run() {
        $tpl = $this->sl->getTemplate();
        $tpl->set('options', $this->options);
        return $tpl->fetch('splashscreen.tpl');
    }
}
