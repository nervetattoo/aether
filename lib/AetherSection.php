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
     * TODO Reconsider the solution with passing in extra tpl data
     * to renderModules() as an argument. Smells bad
     *
     * @access protected
     * @return string
     * @param array $tplVars
     */
    protected function renderModules($tplVars = array()) {
        try {
            // Timer
            $timer = $this->sl->get('timer');
            $timer->timerStart('module_render');
        }
        catch (Exception $e) {
            // No timing, we're in prod
        }
        $config = $this->sl->get('aetherConfig');
        $cache = new Cache(false, true, true);
        /** 
         * Decide cache name for rule based cache
         * If the option cacheas is set, we will use the cache name
         * $domainname_$cacheas
         */
        $url = $this->sl->get('parsedUrl');
        $cacheas = $config->getCacheName();
        if ($cacheas != false)
            $cacheName = $url->get('host') . '_' . $cacheas;
        else
            $cacheName = $url->cacheName();
        $cachetime = $config->getCacheTime();
        /**
         * If one object requests no cache of this request
         * then we need to take that into consideration.
         * If the application frontend and adminpanel lives
         * at the same URL, its crucial that the admin part is
         * not cached and later on displayed to an end user
         */
        $options = $config->getOptions();
        // Support i18n
        $locale = (isset($options['locale'])) ? $options['locale'] : "nb_NO.ISO-8859-1";
        setlocale(LC_ALL, $locale);
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
         * If we have a timer, end this timing
         * we're in test mode and thus showing timing
         * information
         */
        if (is_object($timer))
            $timer->timerTick('module_render', 'read_config');


        $saveCache = true;

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
                // Make tplVars sent in available
                $tpl->setVar("extras", $tplVars);
                $modulesOut = array();
                foreach ($modules as $module) {
                    // If module should be cached, handle it
                    if (array_key_exists('cache', $module) AND !isset($module['noCache'])) {
                        $mCacheName = 
                            $cacheName . $module['name'] ;
                        if ($module['provides'])
                            $mCacheName .= $module['provides'];
                        // Try to read from cache, else generate and cache
                        if (($mOut = $cache->getObject($mCacheName)) == false) {
                            $mCacheTime = $module['cache'];
                            $mod = $module['obj'];
                            try {
                                $mOut = $mod->render();
                                // Run the function 
                                // denyCache to check if some internal module
                                // logic has marked this not to be cached 
                                // while rendering the module
                                if (!$mod->denyCache()) {
                                    $cache->saveObject($mCacheName, $mOut, $mCacheTime);
                                }
                                else {
                                    $saveCache = false;
                                }
                            }
                            catch (Exception $e) {
                                // TODO: Make a special exception for when a 
                                // module fails so terribly that it needs to 
                                // be replaced by an old cached one.
                                $mOut = $cache->getObject($mCacheName, 86400);

                                $saveCache = false;
                                $this->logerror($e);
                                continue;
                            }
                        }
                    }
                    else {
                        // Module shouldn't be cached, just render it without
                        // saving to cache
                        $mod = $module['obj'];
                        try {
                            $mOut = $mod->render();
                            if ($mod->denyCache())
                                $saveCache = false;
                        }
                        catch (Exception $e) {
                            // Make sure page cache isn't saved if a module fails
                            $saveCache = false;
                            $this->logerror($e);
                            continue;
                        }
                    }
                    /**
                     * If this module provides some service
                     * make sure we actually push it
                     */
                    if (array_key_exists('provides', $module)) {
                        $this->provide($module['provides'], $mOut);
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
                    if (array_key_exists('provides', $module)) {
                        $modulesOut[$modName][$module['provides']] = $mOut;
                    }
                    else {
                        $modulesOut[$modName][] = $mOut;
                    }
                    /**
                     * If we have a timer, end this timing
                     * we're in test mode and thus showing timing
                     * information
                     */
                    if (is_object($timer)) {
                        if (array_key_exists('provides', $module))
                            $timerMsg = $module['provides'];
                        else
                            $timerMsg = $modName;
                        $timer->timerTick('module_render', $timerMsg);
                    }
                }
                // Export rendered modules to template
                foreach ($modulesOut as $name => $mod) {
                    if (count($mod) > 1) {
                        $tpl->setVar($name, $mod);
                    }
                    else {
                        $tpl->setVar($name, current($mod));
                    }
                }
            }
            $output = $tpl->returnPage();
            if (is_numeric($cachetime))
                $cache->saveObject($cacheName, $output, $cachetime);
        }
        else {
            $output = $cache->getObject($cacheName);
        }
        /**
         * If we have a timer, end this timing
         * we're in test mode and thus showing timing
         * information
         */
        if (is_object($timer))
            $timer->timerEnd('module_render');
        // Return output
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
        throw new Exception("Failed to locate module [$moduleName]");
    }
    
    /**
     * Provide the output of a module
     *
     * @access public
     * @return void
     * @param string $name
     * @param string $content
     */
    public function provide($name, $content) {
        $vector = $this->sl->getVector('aetherProviders');
        $vector[$name] = $content;
    }
    
    /**
     * Log an error message from an exception to error log
     *
     * @access private
     * @return void
     * @param Exception $e
     */
    private function logerror($e) {
        trigger_error("Caught exception at " . $e->getFile() . ":" . $e->getLine() . ": " . $e->getMessage() . ", trace: " . $e->getTraceAsString());
    }
}

?>
