<?php // vim:set tabstop=4 shiftwidth=4 smarttab expandtab:

/**
 * 
 * Base class definition for aether services
 * 
 * Created: 2007-04-30
 * @author Raymond Julin
 * @package aether.lib
 */

abstract class AetherService {
    
    /**
     * Service locator
     * @var AetherServiceLocator
     */
    protected $sl = null;
    
    /**
     * Specific options for this service
     * @var array
     */
    protected $options = array();
    
    /**
     * Constructor. Accept service locator
     *
     * @access public
     * @return AetherService
     * @param AetherServiceLocator $sl
     * @param array $options
     */
    public function __construct(AetherServiceLocator $sl, $options=array()) {
        $this->sl = $sl;
        $this->options = $options;
    }
    
    /**
     * Render services.
     *
     * @access public
     * @return string
     */
    abstract public function render();
}
?>
