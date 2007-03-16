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
     * Createa instance of module
     *
     * @access public
     * @return AetherModule
     * @param string $module
     * @param AetherServiceLocator $sl
     */
    public static function create($module, AetherServiceLocator $sl) {
        $module = 'AetherModule' . ucfirst($module);
        $file = AETHER_PATH . 'modules/' . $module . '.php';
        if (file_exists($file)) {
            include($file);
            $mod = new $module($sl);
            return $mod;
        }
        else {
            throw new Exception("Module '$file' does not exist");
        }
    }
}
?>
