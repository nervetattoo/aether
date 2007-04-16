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
        $config = $this->sl->fetchCustomObject('aetherConfig');
        $cache = new Cache;
        $cachetime = $config->getCacheTime();
        $cacheName = $this->sl->fetchCustomObject('parsedUrl')->__toString();
        if (!is_numeric($cachetime) OR ($output = $cache->getObject($cacheName) == false)) {
            /* Load controller template
             * This template knows where all modules should be placed
             * and have internal wrapping html for this section
             */
            $tplInfo = $config->getTemplate();
            $options = $config->getOptions();
            $searchPath = (isset($options['searchpath'])) 
                ? $options['searchpath'] : AETHER_PATH;
            $tpl = $this->sl->getTemplate($tplInfo['setId']);
            $modules = $config->getModules();
            if (is_array($modules)) {
                $tpl->selectTemplate($tplInfo['name']);
                foreach ($modules as $module) {
                    // If module should be cached, handle it
                    if (!isset($module['options']))
                        $module['options'] = array();
                    AetherModuleFactory::$path = $searchPath;
                    if (array_key_exists('cache', $module)) {
                        $mCacheName = 
                            $cacheName . "_" . $module['name'] ;
                        // Try to read from cache, else generate and cache
                        if (($mOut = $cache->getObject($mCacheName)) == false) {
                            $mCacheTime = $module['cache'];
                            $mod = AetherModuleFactory::create(
                                    $module['name'], $this->sl,
                                    $module['options']);
                            $mOut = $mod->render();
                            $cache->saveObject($mCacheName, $mOut, $mCacheTime);
                        }
                    }
                    else {
                        $mod = AetherModuleFactory::create(
                                $module['name'], $this->sl,
                                $module['options']);
                        $mOut = $mod->render();
                    }
                    $tpl->setVar($module['name'], $mOut);
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
}

?>
