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
require_once(LIB_PATH . 'SessionHandler.lib.php');
require_once(AETHER_PATH . 'lib/AetherUser.php');
require_once(AETHER_PATH . 'lib/AetherServiceLocator.php');
require_once(AETHER_PATH . 'lib/AetherUrlParser.php');
require_once(AETHER_PATH . 'lib/AetherConfig.php');
require_once(AETHER_PATH . 'lib/AetherSectionFactory.php');
require_once(AETHER_PATH . 'lib/AetherSection.php');
require_once(AETHER_PATH . 'lib/AetherTextResponse.php');
require_once(AETHER_PATH . 'lib/AetherModule.php');
require_once(AETHER_PATH . 'lib/AetherModuleFactory.php');

/**
 * 
 * Main class for Aether.
 * Fires up the Aether system and delegates down to
 * section/subsection that is requested based on the
 * rules.
 * 
 * Created: 2007-01-31
 * @author Raymond Julin
 * @package aether
 */

class Aether {
    
    /**
     * Hold service locator
     * @var AetherServiceLocator
     */
    private $sl = null;
    
    /**
     * Section
     * @var AetherSection
     */
    private $section = null;
    
    /**
     * Constructor. 
     * Parses url, prepares everything
     *
     * @access public
     * @return Aether
     */
    public function __construct() {
        // Initiate all required helper objects
        $this->sl = new AetherServiceLocator;
        $parsedUrl = new AetherUrlParser;
        $parsedUrl->parseServerArray($_SERVER);
        $this->sl->set('parsedUrl', $parsedUrl);
        $config = new AetherConfig($parsedUrl, AETHER_PATH . 'aether.config.xml');
        $this->sl->set('aetherConfig', $config);
        // Construct session
        $session = new SessionHandler;
        $this->sl->set('session', $session);
        // If a user is associated to the session, create user object
        if (is_numeric($session->get('userId'))) {
            $user = new AetherUser($this->sl, $session->get('userId'));
            $this->sl->set('user', $user);
        }

        // Initiate section/subsection
        try {
            $options = $config->getOptions();
            $searchPath = (isset($options['searchpath'])) 
                ? $options['searchpath'] : AETHER_PATH;
            AetherSectionFactory::$path = $searchPath;
            $this->section = AetherSectionFactory::create(
                $config->getSection(), 
                $this->sl
            );
            $this->sl->set('section', $this->section);
        }
        catch (Exception $e) {
            // Failed to load section, what to do?
            exit('Failed horribly: ' . $e->getMessage());
        }
    }
    
    /**
     * Render aether system
     * Initialization point. When render() is called
     * everything in the chain of actions is performed one by one
     * untill we have a response to serve the user
     *
     * @access public
     * @return string
     */
    public function render() {
        $response = $this->section->response();
        $session = $this->sl->get('session');
        $session->set('wasGoingTo', $_SERVER['REQUEST_URI']);
        //$this->sl->getDatabase('prisguide')->debug->printLog();
        $response->draw();
        // Just for the fun of it, print how much queries we ran
        /*
        echo "<pre>";
        print_r($this->sl->getDatabase('prisguide')->count);
        echo "</pre>";
        */
    }
}
?>
