<?php // vim:set tabstop=4 shiftwidth=4 smarttab expandtab:

/**
 * 
 * Create an aether service
 * 
 * Created: 2007-04-30
 * @author Raymond Julin
 * @package aether
 */

class AetherServiceFactory {
    
    /**
     * The path to search for modules in
     * @var 
     */
    static public $path = AETHER_PATH;
    
    /**
     * Create instance of service
     *
     * @access public
     * @return AetherService
     * @param string $service
     * @param AetherServiceLocator $sl
     * @param array $options
     */
    public static function create($service, AetherServiceLocator $sl, $options=array()) {
        $module = 'AetherService' . ucfirst($module);
        if (!strpos(self::$path, ';'))
            $paths = array(self::$path);
        else {
            $paths = array_map('trim', explode(';', self::$path));
        }
        foreach ($paths as $path) {
            $file = $path . 'services/' . $module . '.php';
            if (file_exists($file)) {
                include_once($file);
                $mod = new $module($sl, $options);
                return $mod;
            }
        }
        throw new Exception("Service '$file' does not exist");
    }
}
?>
