<?php
/*
HARDWARE.NO EDITORSETTINGS:
vim:set tabstop=4:
vim:set shiftwidth=4:
vim:set smarttab:
vim:set expandtab:
*/

require_once('/home/lib/libDefines.lib.php');
require_once(LIB_PATH . 'Cache.lib.php');
require_once(AETHER_PATH . 'lib/AetherResponse.php');

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
     * Hold service locator
     * @var AetherServiceLocator
     */
    protected $sl = null;
    
    /**
     * COnstructor. Accept subsection
     *
     * @access public
     * @return AetherSection
     * @param AetherServiceLocator $sl
     */
    public function __construct(AetherServiceLocator $sl) {
        $this->sl = $sl;
    }
    
    /**
     * Process the sections business logic that should happen even
     * when no rendering is neccessary (logging, statistics etc)
     *
     * @access protected
     * @return void
     */
    protected function process() {
    }
    
    /**
     * Render content from modules
     * this is where caching is implemented
     * TODO Possible refactoring, many leves of nesting
     *
     * @access protected
     * @return string
     */
    protected function renderModules() {
        $config = $this->sl->get('aetherConfig');
        $cache = new Cache;
        $cachetime = $config->getCacheTime();
        $cacheName = $this->sl->get('parsedUrl')->__toString();
        /**
         * If one object requests no cache of this request
         * then we need to take that into consideration.
         * If the application frontend and adminpanel lives
         * at the same URL, its crucial that the admin part is
         * not cached and later on displayed to an end user
         */
        $options = $config->getOptions();
        // Support custom searchpaths
        $searchPath = (isset($options['searchpath'])) 
            ? $options['searchpath'] : AETHER_PATH;
        AetherModuleFactory::$path = $searchPath;
        $mods = $config->getModules();
        $modules = array(); // Final array over modules
        foreach ($mods as $module) {
            if (!isset($module['options']))
                $module['options'] = array();
            // Get module object
            $object = AetherModuleFactory::create($module['name'], 
                    $this->sl, $module['options']);
            // If the module, in this setting, blocks caching, accept
            if ($object->denyCache()) {
                $module['noCache'] = true;
                $cachetime = false;
            }
            $module['obj'] = $object;
            $modules[] = $module;
        }
        /**
         * Render page
         */
        if (!is_numeric($cachetime) OR ($output = $cache->getObject($cacheName) == false)) {
            /* Load controller template
             * This template knows where all modules should be placed
             * and have internal wrapping html for this section
             */
            $tplInfo = $config->getTemplate();
            $tpl = $this->sl->getTemplate($tplInfo['setId']);
            if (is_array($modules)) {
                $tpl->selectTemplate($tplInfo['name']);
                $modulesOut = array();
                foreach ($modules as $module) {
                    // If module should be cached, handle it
                    if (array_key_exists('cache', $module) AND !isset($module['noCache'])) {
                        $mCacheName = 
                            $cacheName . "_" . $module['name'] ;
                        if ($module['surname'])
                            $mCacheName .= '_' . $module['surname'];
                        // Try to read from cache, else generate and cache
                        if (($mOut = $cache->getObject($mCacheName)) == false) {
                            $mCacheTime = $module['cache'];
                            $mod = $module['obj'];
                            $mOut = $mod->render();
                            $cache->saveObject($mCacheName, $mOut, $mCacheTime);
                        }
                    }
                    else {
                        $mod = $module['obj'];
                        $mOut = $mod->render();
                    }
                    /**
                     * Support multiple modules of same type by 
                     * specificaly naming them with a surname when
                     * duplicates are encountered
                     */
                    $modName = $module['name'];
                    if (!isset($modulesOut[$modName])) {
                        $modulesOut[$modName] = array();
                    }
                    if ($module['surname']) {
                        $modulesOut[$modName][$module['surname']] = $mOut;
                    }
                    else {
                        $modulesOut[$modName][] = $mOut;
                    }
                }
                // Export rendered modules to template
                foreach ($modulesOut as $name => $mod) {
                    if (count($mod) > 1)
                        $tpl->setVar($name, $mod);
                    else
                        $tpl->setVar($name, current($mod));
                }
            }
            $output = $tpl->returnPage();
            if (is_numeric($cachetime))
                $cache->saveObject($cacheName, $output, $cachetime);
        }
        else {
            $output = $cache->getObject($cacheName);
        }
        return $output;
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
    
    /**
     * Render service
     *
     * @access public
     * @return AetherResponse
     * @param string $moduleName
     * @param string $serviceName Name of service
     */
    public function service($moduleName, $serviceName) {
        // Locate module containing service
        $config = $this->sl->get('aetherConfig');
        $options = $config->getOptions();
        // Support custom searchpaths
        $searchPath = (isset($options['searchpath'])) 
            ? $options['searchpath'] : AETHER_PATH;
        AetherModuleFactory::$path = $searchPath;

        // Create module
        $mod = null;
        foreach ($config->getModules() as $module) {
            if ($module['name'] != $moduleName)
                continue;
            if (!isset($module['options']))
                $module['options'] = array();
            // Get module object
            $mod = AetherModuleFactory::create($module['name'], 
                    $this->sl, $module['options']);
            break;
        }
        // Run service
        if ($mod instanceof AetherModule) {
            // Run service
            return $mod->service($serviceName);
        }
        throw Exception("Failed to locate module [$moduleName]");
    }
}

?>
