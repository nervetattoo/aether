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
 * Entry point to all priceguide applicatino sections
 * 
 * Created: 2007-02-02
 * @author Raymond Julin
 * @package aether.sections
 */

class AetherSection {
}

class AetherSectionPriceguide extends AetherSection {
    public function __construct($foo) {
        $this->subsection = $foo;
    }

    public function getSubSection() {
        return $this->subsection;
    }
}
?>
