<?php // vim:set ts=4 sw=4 et:

/**
 * 
 * Dummy module for overwriting other modules
 * 
 * Created: 2008-10-09
 * @author Mads Erik Forberg
 * @package
 */

class AetherModuleDummy extends AetherModule {
    
    /**
     * Render
     *
     * @access public
     * @return bool
     */

     public function run() {
        return false;
     }
}
