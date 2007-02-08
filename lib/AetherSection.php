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
     *
     * @access protected
     * @return string
     */
    protected function renderModules() {
        /* Load controller template
         * This template basicaly knows where all modules should be placed
         * and have internal wrapping html for this section
         */
        $config = $this->sl->fetchCustomObject('aetherConfig');
        $cache = new Cache;
        $cachetime = $config->getCacheTime();
        $cacheName = $this->sl->fetchCustomObject('parsedUrl')->__toString();
        if (!is_numeric($cachetime) OR ($output = $cache->getObject($cacheName) == false)) {
            $tplInfo = $config->getTemplate();
            $tpl = $this->sl->getTemplate($tplInfo['setId']);
            $modules = $config->getModules();
            if (is_array($modules)) {
                $tpl->selectTemplate($tplInfo['name']);
                foreach ($modules as $module) {
                    $mod = AetherModuleFactory::create($module['name'], $this->sl);
                    $tpl->setVar($module['name'], $mod->render());
                }
            }
            $output = $tpl->returnPage();
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
