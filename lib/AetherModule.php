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
 * Base class definition for aether modules
 * 
 * Created: 2007-02-06
 * @author Raymond Julin
 * @package aether.lib
 */

abstract class AetherModule {
    
    /**
     * Hold service locator
     * @var AetherServiceLocator
     */
    protected $sl = null;
    
    /**
     * Specific options for this module
     * @var array
     */
    protected $options = array();
    
    /**
     * Constructor. Accept service locator
     *
     * @access public
     * @return AetherModule
     * @param AetherServiceLocator $sl
     * @param array $options
     */
    public function __construct(AetherServiceLocator $sl, $options=array()) {
        $this->sl = $sl;
        $this->options = $options;
    }
    
    /**
     * Render module.
     * Modules is only capable of returning text ouput
     * any http actions must be taken by the section
     *
     * @access public
     * @return string
     */
    abstract public function render();
    
    /**
     * Allow each module to decide if caching should be totaly
     * forbidden in a given context. Useful for modules
     * that deliver highly dynamic data based on each request
     * but where it is generic in certain cases
     *
     * @access public
     * @return bool
     */
    public function denyCache() {
        return false;
    }
    
    /**
     * Render a given service
     *
     * @access public
     * @return AetherResponse
     */
    abstract public function service($name);
}
?>
