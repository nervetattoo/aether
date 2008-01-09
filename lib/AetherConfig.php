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
     * XMLDoc
     * @var DOMDocument
     */
    private $doc;
    
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
    private $cacheas = false;
    
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
     * Whats left of the request path when url parsing is finished
     * @var array
     */
    private $path;
    
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
        if (!file_exists($configFilePath)) {
            throw new AetherMissingFileException(
                "Config file [$configFilePath] is missing.");
        }
        $doc = new DOMDocument;
        $doc->preserveWhiteSpace = false;
        $doc->load($configFilePath);
        $this->doc = $doc;
        $xpath = new DOMXPath($doc);


        /*
         * Find starting point of url rules, from this point on we
         * use recursion to decide on the correct rule to use
         */
        $sitename = $url->get('host');
        $xquery = "/config/site[@name='$sitename']";
        $xquery .= '/urlRules/*';
        $nodelist = $xpath->query($xquery);
        // Fallback to the "any" site qualifier
        if ($nodelist->length == 0) {
            $sitename = '*';
            $xquery = "/config/site[@name='$sitename']/urlRules/*";
            $nodelist = $xpath->query($xquery);
        }
        // Subtract global options
        $ruleBase = " | /config/site[@name='$sitename']/urlRules/";
        $xquery = "/config/site[@name='$sitename']/option";
        $xquery .= $ruleBase . 'section';
        $xquery .= $ruleBase . 'template';
        $xquery .= $ruleBase . 'module';
        $xquery .= $ruleBase . 'option';
        $optionList = $xpath->query($xquery);
        if ($optionList->length > 0)
            $this->subtractNodeConfiguration($optionList);
        $path = $url->get('path');
        if (substr($path, -1) == '/')
            $path = substr($path, 0, -1);
        $node = $this->findMatchingConfigNode(
            $nodelist, 
            explode('/',substr($path,1))
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
                if ($this->matches($current, $node)) {
                    /* If this node is a match, and has child nodes
                     * then try to crawl the next level aswell, see
                     * if a more exact match is possible
                     */
                    if ($this->hasChildRules($node)) {
                        /**
                         * Fetch global options from this scope
                         * Options can be set on any level and
                         * overridden on deeper levels if set there
                         */
                        $this->subtractNodeConfiguration($node);
                        if ($this->findMatchingConfigNode($node->childNodes, $path)) {
                            return true;
                        }
                        else {
                            $this->path = $path;
                            return true;
                        }
                    }
                    else {
                        $this->subtractNodeConfiguration($node);
                        $this->path = $path;
                        return true;
                    }
                }
                elseif ($node->hasAttribute('default')) {
                    /* This is the default match if we dont find any
                     * better matches on this level
                     */
                    $this->subtractNodeConfiguration($node);
                    $this->path = $path;
                    return false;
                }
            }
        }
        /**
         * If we reach this point it means NO rules truly matched
         * not even a default rule. Damn bastard developer who doesnt
         * provide a default rule in your app!!!
         */
        throw new AetherNoUrlRuleMatchException(
            "No rules matches this url. App.config error");
    }
    
    /**
     * Test if a <rule> node have other <rule>s below it
     * cause if it does we need to search further down
     * for a match, if not we can simply stop.
     *
     * @access private
     * @return bool
     * @param object $node
     */
    private function hasChildRules($node) {
        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $child) {
                if ($child->nodeName == 'rule') {
                    return true;
                }
            }
            return false;
        }
        else {
            return false;
        }
    }
    
    /**
     * Check if an url fragment matches the match or pattern
     * attribute for an url rule.
     * There are two ways to apply url rule matching:
     * Match: A simple string checking if the url part exactly
     * matches the match attribute. Very good for defining sections
     * like "video" or "articles"
     * Pattern: A full fledged PCRE match. Suited when you need to
     * assure the matching part only consists of numbers, or that
     * it doesnt contain special signs, or need to be a minimum length
     * When using pattern matching you need to type a valid regex, 
     * making it harder to use: pattern="/[0-9]+/"
     *
     * @access private
     * @return bool
     * @param string $check
     * @param object $node
     */
    private function matches($check, $node) {
        if (!empty($check)) {
            if ($node->hasAttribute('match')) {
                $matches = $node->getAttribute('match') == $check;
            }
            elseif ($node->hasAttribute('pattern')) {
                $matches = preg_match(
                    $node->getAttribute('pattern'), $check);
            }
            if ($matches) {
                // Store value of url fragment, typical stores and id
                if ($node->hasAttribute('store')) {
                    $this->storeVariable(
                        $node->getAttribute('store'), $check);
                }
                // Remember the url base if this is it
                if ($node->hasAttribute('isBase')) {
                    $this->urlBase .= $check.'/';
                }
                return true;
            }
        }
        return false;
    }
    
    /**
     * Given a nodelist, subtract section, subsection and other data
     * from that node and store it in self
     *
     * @access private
     * @return void
     * @param DOMNode $node
     */
     private function subtractNodeConfiguration($node) {
        if ($node instanceof DOMNode) {
            if ($node->hasAttribute('cache'))
                $this->cache = $node->getAttribute('cache');
            if ($node->hasAttribute('cacheas'))
                $this->cacheas = $node->getAttribute('cacheas');
            $nodelist = $node->childNodes;
        }
        else {
            $nodelist = $node;
        }
        foreach ($nodelist as $child) {
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
                    // Modules can contain options, which we need to take into account
                    $text = '';
                    $opts = array();
                    foreach ($child->childNodes as $option) {
                        if ($option->nodeName == '#text')
                            $text .= $option->nodeValue;
                        if ($option->nodeName == 'option')
                            $opts[$option->getAttribute('name')] = $option->nodeValue;
                    }
                        
                    // Merge options from all scopes together
                    $options = array_merge($this->options, $opts);
                    $module = array();
                    //$module = array(
                    $module['name'] = trim($text);
                    $module['options'] = $options;
                    $module['_'] = null;
                    if (!isset($cache)) {
                        if ($child->hasAttribute('cache'))
                            $module['cache'] = $child->getAttribute('cache');
                    }
                    /**
                     * A module could provide itself under a fake name
                     * For example AmobilHeader could be provided as
                     * "header", this object would then be usable
                     * elsewhere in the application
                     */
                    if ($child->hasAttribute('provides'))
                        $module['provides'] = trim($child->getAttribute('provides'));

                    /**
                     * In order to support multiple instances of the
                     * same module, support naming each module
                     */
                    if ($child->hasAttribute('name'))
                        $module['surname'] = trim($child->getAttribute('name'));
                    $this->modules[] = $module;
                    break;

                case 'option':
                    $name = $child->getAttribute('name');
                    // Support additive options
                    $mode = "overwrite";
                    if ($child->hasAttribute("mode")) {
                        if (array_key_exists($name, $this->options)) {
                            $mode = $child->getAttribute("mode");
                            $prev = array_map(
                                "trim", explode(";", $this->options[$name]));
                            $opts = array_map(
                                "trim", explode(";", $child->nodeValue));
                        }
                    }
                    switch ($mode) {
                        case 'add':
                            /**
                             * If mode is "add", add to ; separated list
                             * and ensure no duplicates are created?
                             */
                            // Add everything that doesnt create dupes
                            foreach ($opts as $opt) {
                                if (!in_array($opt, $prev))
                                    $prev[] = $opt;
                            }
                            $value = implode(";", $prev);
                            break;
                        case 'del':
                             // If mode is "del", delete from ; list
                            $value = implode(";", array_diff($prev, $opts));
                            break;
                        default:
                            // Simple string/int value
                            $value = trim($child->nodeValue);
                            break;
                    }
                    $this->options[$name] = $value;
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
    public function getCacheName() {
        return $this->cacheas;
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
        // Reorder modules so providers are first
        $regular = array();
        $providers = array();
        foreach ($this->modules as $module) {
            if (array_key_exists('provides', $module))
                $providers[] = $module;
            else
                $regular[] = $module;
            
        }
        return array_merge($providers, $regular);
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
    
    /**
     * Fetch whats left and "unusued" of the path originaly requested
     *
     * @access public
     * @return array
     */
    public function getPathLeftOvers() {
        return $this->path;
    }
    
    /**
     * Get what mode to operate in
     *
     * @access public
     * @return string
     */
    public function mode() {
        return $this->mode;
    }
}
?>
