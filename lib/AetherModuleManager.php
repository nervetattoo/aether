<?php // vim:set ts=4 sw=4 et:

require_once(LIB_PATH . 'Cache.lib.php');

/**
 * Keep track over all loaded modules and make sure every
 * module gets called at the stage theyre supposed to 
 * 
 * Created: 2008-05-01
 * @author Raymond Julin
 * @package aether.lib
 */

class AetherModuleManager {
    private $sl;
    
    /**
     * Based on config file, prepare what modules goes where
     *
     * @access public
     * @return AetherModuleManager
     * @param AetherServiceLocator $sl
     */
    public function __construct(AetherServiceLocator $sl) {
        $this->sl = $sl;
    }

    
    /**
     * This gets called as early as possible in the aether execution
     * sequence.
     *
     * @access public
     * @return void
     */
    public function start() {
    }
    public function run() {
    }
    
    
    /**
     * Runs modules stop() method where it is present
     *
     * @access public
     * @return void
     */
    public function stop() {
        // Read module map to find what modules to run by here
        $config = $this->sl->get('aetherConfig');
        $cache = new Cache(false, true, true);
        $cacheName = 'module_map' . 
            str_replace('/', '_', $config->configFilePath());
        $modMap = $cache->getObject($cacheName, 131400000);
        // If there are stop instructions, run them
        if (count($modMap['stop']) > 0) {
            $options = $config->getOptions();
            // Support custom searchpaths
            $searchPath = (isset($options['searchpath'])) 
                ? $options['searchpath'] : AETHER_PATH;
            AetherModuleFactory::$path = $searchPath;
            $mods = $config->getModules();
            $modules = array(); // Final array over modules
            foreach ($mods as $mod) {
                $modName = 'AetherModule' . $mod['name'];
                if (!in_array($modName, $modMap['stop']))
                    continue;
                if (!isset($mod['options']))
                    $mod['options'] = array();
                // Get module object
                $module = AetherModuleFactory::create($mod['name'], 
                        $this->sl, $mod['options']);
                // Run module->stop
                $module->stop();
            }
        }
    }
}
?>
