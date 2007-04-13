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
 * Factory for loading modules
 * 
 * Created: 2007-02-06
 * @author Raymond Julin
 * @package aether.lib
 */

class AetherModuleFactory {
    
    /**
     * The path to search for modules in
     * @var 
     */
    static public $path = AETHER_PATH;
    
    /**
     * Createa instance of module
     *
     * @access public
     * @return AetherModule
     * @param string $module
     * @param AetherServiceLocator $sl
     * @param array $options
     */
    public static function create($module, AetherServiceLocator $sl, $options=array()) {
        $module = 'AetherModule' . ucfirst($module);
        $file = self::$path . 'modules/' . $module . '.php';
        if (file_exists($file)) {
            include($file);
            $mod = new $module($sl, $options);
            return $mod;
        }
        else {
            throw new Exception("Module '$file' does not exist");
        }
    }
}
?>
