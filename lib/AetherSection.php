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
 * Base class definition of aether sections
 * 
 * Created: 2007-02-05
 * @author Raymond Julin
 * @package aether.lib
 */

abstract class AetherSection {
    
    /**
     * Hold current active subsection
     * @var AetherSubSection
     */
    private $subsection = null;
    
    /**
     * COnstructor. Accept subsection
     *
     * @access public
     * @return AetherSection
     * @param AetherSubSection $subsection
     */
    public function __construct(AetherSubSection $subsection) {
        $this->subsection = $subsection;
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
    abstract public function response() {}
    
    /**
     * Get contained subsection
     *
     * @access public
     * @return AetherSubSection
     */
    public function getSubSection() {
        return $this->subsection;
    }
}

?>
