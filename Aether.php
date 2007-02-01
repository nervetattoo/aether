<?php
/*
HARDWARE.NO EDITORSETTINGS:
vim:set tabstop=4:
vim:set shiftwidth=4:
vim:set smarttab:
vim:set expandtab:
*/

require_once('/home/lib/libDefines.lib.php');
require_once(AETHER_PATH . 'lib/AetherServiceLocator.php');
require_once(AETHER_PATH . 'lib/AetherUrlParser.php');

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
     * Constructor. 
     * Parses url, prepares everything
     *
     * @access public
     * @return Aether
     */
    public function __construct() {
        $this->sl = new AetherServiceLocator;
        $parsedUrl = new AetherUrlParser;
        // Attach url parts to a complete url
        $url = '';
        if (preg_match('/http\//i', $_SERVER['SERVER_PROTOCOL']))
            $url = 'http://';
        $url .= $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
        $parsedUrl->parse($url);
        $this->sl->saveCustomObject('parsedUrl', $parsedUrl);
        //$config = new AetherConfig($this->sl)
        /*
        $doc = new DOMDocument;
        $doc->preserveWhiteSPace = false;
        $doc->Load('aether.config.xml');
        $xpath = new DOMXPath($doc);
        $foo = $xpath->query("//site[@name='aether.raymond.raw.no']/urlRules/rule");
        */
        // Read config and find matching 
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
    }
}
?>
