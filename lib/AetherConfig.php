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
 * Read in config file for aether and make its options
 * available for the system
 * 
 * Created: 2007-02-01
 * @author Raymond Julin
 * @package aether
 */

class AetherConfig {
    
    /**
     * What section was found
     * @var string
     */
    private $section;
    
    /**
     * How long should this section be cached
     * @var int/false
     */
    private $cache = false;
    
    /**
     * What control template should be used for layout
     * @var string
     */
    private $template;
    
    /**
     * What modules should be included
     * @var array
     */
    private $modules = array();
    
    /**
     * Option settings for this section (highly optional)
     * @var array
     */
    private $options = array();
    
    /**
     * Variables found in the url
     * @var arra
     */
    private $urlVariables = array();
    
    /**
     * If set, a specific base to be used for all urls within app
     * @var string
     */
    private $urlBase = '/';
    
    /**
     * Constructor.
     *
     * @access public
     * @return AetherConfig
     * @param AetherUrlParser $url
     * @param string $configFilePath
     */
    public function __construct(AetherUrlParser $url, $configFilePath) {
        $doc = new DOMDocument;
        $doc->preserveWhiteSpace = false;
        $doc->Load($configFilePath);
        $xpath = new DOMXPath($doc);


        /*
         * Iterate over all parts of url, creating an increasingly more specific
         * xpath query, on the way run this query, and if succesfull, continue
         * running over the parts. Once a failure happens, stop as we
         * have the closest matching node
         */
        $xquery = "//config/site[@name='" . $url->get('host') . "']";
        $xquery .= '/urlRules/*';
        $nodelist = $xpath->query($xquery);
        $node = $this->findMatchingConfigNode(
            $nodelist, 
            explode('/',substr($url->get('path'),1))
        );
    }
    
    /**
     * Find matching config node from nodelist and path
     *
     * @access private
     * @return node
     * @param DOMNodeList $list
     * @param array $path
     */
    private function findMatchingConfigNode($list, $path) {
        // Find first non empty part in path
        if (is_array($path)) {
            foreach ($path as $key => $current) {
                if (!empty($current)) {
                    $path = array_slice($path, $key+1);
                    break;
                }
            }
        }
        // Crawl the config hierarchy till the right node is found
        foreach ($list as $node) {
            // This have to be a DOMElement
            if ($node instanceof DOMElement) {
                /* If the attribute match is not set theres no point
                 * in searching through this path
                 * Check if this actually matches the current part
                 * of the path we are examining ($current)
                 */
                if ($node->hasAttribute('match') AND
                    !empty($current) AND
                    ($node->getAttribute('match') == $current OR
                    substr($node->getAttribute('match'),0,1) == '$')) {
                    /* If the match attribute actually contained a $
                     * at the start, then this value should be saved
                     * as a variable
                     */
                    $match = $node->getAttribute('match');
                    if ($match[0] == '$') {
                        $varName = substr($match, 1);
                        $this->storeVariable($varName, $current);
                    }
                    /* If the isBase attribute is set to true,
                     * then the match attribute should be used as
                     * the base for all urls used for all subsequent
                     * rule matches
                     */
                    if ($node->hasAttribute('isBase')) {
                        // We cant have wildcards as url base
                        if ($match[0] != '$')
                            $this->urlBase = '/'.$match.'/';
                    }
                    /* If this node is a match, and has child nodes
                     * then try to crawl the next level aswell, see
                     * if a more exact match is possible
                     */
                    if ($node->hasChildNodes()) {
                        if ($this->findMatchingConfigNode($node->childNodes, $path)) {
                            return true;
                        }
                        else {
                            $this->subtractNodeConfiguration($node);
                            return true;
                        }
                    }
                    else {
                        $this->subtractNodeConfiguration($node);
                        return true;
                    }
                }
                elseif ($node->hasAttribute('default')) {
                    /* This is the default match if we dont find any
                     * better matches on this level
                     */
                    $this->subtractNodeConfiguration($node);
                }
            }
        }
    }
    
    /**
     * Given a nodelist, subtract section, subsection and other data
     * from that node and store it in self
     *
     * @access private
     * @return void
     * @param DOMNode $node
     */
     private function subtractNodeConfiguration(DOMNode $node) {
        if ($node->hasAttribute('cache')) {
            $cache = $node->getAttribute('cache');
            $this->cache = $cache;
        }
        foreach ($node->childNodes as $child) {
            if ($child instanceof DOMText)
                continue;
            switch ($child->nodeName) {
                case 'section': 
                    $this->section = $child->nodeValue;
                    break;
                case 'template':
                    $tpl = array();
                    $tpl['setId'] = $child->getAttribute('id');
                    $tpl['name'] = $child->nodeValue;
                    $this->template = $tpl;
                    break;
                case 'module':
                    $module = array('name' => $child->nodeValue);
                    if (!isset($cache)) {
                        if ($child->hasAttribute('cache'))
                            $module['cache'] = $child->getAttribute('cache');
                    }
                    $this->modules[] = $module;
                    break;
                case 'option':
                    $this->options[$child->getAttribute('name')] =
                        $child->nodeValue;
                    break;
            }
        }
    }
    
    /**
     * Store a variable fetched from the url
     *
     * @access private
     * @return void
     * @param string $key
     * @param mixed $val
     */
    public function storeVariable($key, $val) {
        $this->urlVariables[$key] = $val;
    }
    
    /**
     * Get section
     *
     * @access public
     * @return string
     */
    public function getSection() {
        return $this->section;
    }
    
    /**
     * Get cache time
     *
     * @access public
     * @return int/bool
     */
    public function getCacheTime() {
        return $this->cache;
    }
    
    /**
     * Get requested control templates name
     *
     * @access public
     * @return string
     */
    public function getTemplate() {
        return $this->template;
    }
    
    /**
     * Get array over what modules should be used when rendering page
     *
     * @access public
     * @return array
     */
    public function getModules() {
        return $this->modules;
    }
    
    /**
     * Get all options set for section
     *
     * @access public
     * @return array
     */
    public function getOptions() {
        return $this->options;
    }
    
    /**
     * Get an url variable
     *
     * @access public
     * @return mixed
     * @param string $key
     */
    public function getUrlVariable($key) {
        if (array_key_exists($key, $this->urlVariables)) {
            return $this->urlVariables[$key];
        }
        else {
            throw new Exception("[$key] is not an existing variable");
        }
    }
    
    /**
     * Fetch url base
     *
     * @access public
     * @return string
     */
    public function getBase() {
        return $this->urlBase;
    }
}
?>
