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
 * Factory for creating instances of section objects
 * Usage:
 * <code>
 * $section = AetherSectionFactory::create('Priceguide');
 * </code>
 * 
 * Created: 2007-02-02
 * @author Raymond Julin
 * @package aether.lib
 */

class AetherSectionFactory {
    
    /**
     * The path to search for sections in
     * @var 
     */
    static public $path = AETHER_PATH;
     
    /**
     * Create an instance of a section/subsection combination
     *
     * @access public
     * @return AetherSection
     * @param string $section
     * @param AetherServiceLocator $sl
     */
    public static function create($section, AetherServiceLocator $sl) {
        $section = 'AetherSection' . ucfirst($section);
        $file = self::$path . 'sections/' . $section . '.php';
        if (file_exists($file)) {
            include($file);
            $aetherSection = new $section($sl);
            return $aetherSection;
        }
        else {
            throw new Exception('Section and Subsection does not exists');
        }
    }
}
?>
