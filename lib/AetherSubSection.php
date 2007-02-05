<?php
/*
HARDWARE.NO EDITORSETTINGS:
vim:set tabstop=4:
vim:set shiftwidth=4:
vim:set smarttab:
vim:set expandtab:
*/

/**
 * 
 * Base sub section class
 * 
 * Created: 2007-02-05
 * @author Raymond Julin
 * @package aether.lib
 */

abstract class AetherSubSection {
    /**
     * Hold service locator
     * @var AetherServiceLocator
     */
    protected $sl = null;
    
    /**
     * COnstructor. Accept service locator
     *
     * @access public
     * @return AetherSubSection
     * @param AetherServiceLocator $subsection
     */
    public function __construct(AetherServiceLocator $sl) {
        $this->sl = $sl;
    }

    /**
     * Render this section
     * Returns a Response object which can contain a text response or
     * a header redirect response
     * The advantages to using response objects is to more cleanly
     * supporting header() redirects. In other words; more response
     * types
     *
     * @access public
     * @return AetherResponse
     */
    abstract public function response();
}
?>
