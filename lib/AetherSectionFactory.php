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
 * $section = AetherSectionFactory::create('Priceguide', $subsection);
 * </code>
 * 
 * Created: 2007-02-02
 * @author Raymond Julin
 * @package aether.lib
 */

class AetherSectionFactory {
     
    /**
     * Create an instance of a section/subsection combination
     *
     * @access public
     * @return AetherSection
     * @param string $section
     * @param string $subsection
     */
    public static function create($section, $subsection) {
        $section = 'AetherSection' . ucfirst($section);
        $subsection = $section . ucfirst($subsection);
        if (self::exists($section, $subsection)) {
            self::includeFile($section . '/' . $subsection);
            self::includeFile($section);
            $aetherSection = new $section(new $subsection);
            return $aetherSection;
        }
        else {
            throw new Exception('Section and Subsection does not exists');
        }
    }
    
    /**
     * Check if a section exists (if the file exists)
     *
     * @access private
     * @return bool
     * @param string $section
     * @param string $subsection
     */
    private static function exists($section, $subsection) {
        $subsection = AETHER_PATH . 'sections/' . $section . '/' . $subsection . '.php';
        $section = AETHER_PATH . 'sections/' . $section . '.php';
        return (file_exists($section) AND file_exists($subsection));
    }
    
    /**
     * Include the requested file
     *
     * @access private
     * @return void
     * @param string $name
     */
    private static function includeFile($name) {
        $file = AETHER_PATH . 'sections/' . $name . '.php';
        include_once($file);
    }
}
?>
