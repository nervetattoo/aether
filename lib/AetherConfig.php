<?php // vim:set ts=4 sw=4 et:
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
     * Default rule
     * @var Object
     */
    private $defaultRule = false;
    
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
    private $urlRoot = '/';
    
    /**
     * When matching parts of the url, should the path fragment
     * maintain its first slash or not.
     * @var string "keep" or "skip"
     */
    private $slashMode = "skip";
    
    /**
     * Hold config file path
     * @var string
     */
    private $configFilePath;
    
    /**
     * Constructor.
     *
     * @access public
     * @return AetherConfig
     * @param string $configFilePath
     */
    public function __construct($configFilePath) {
        $this->configFilePath = $configFilePath;
    }

    /**
     * Match an url against this config
     *
     * @access public
     * @return bool
     * @param AetherUrlParser $url
     */
    public function matchUrl(AetherUrlParser $url) {
        $configFilePath = $this->configFilePath;
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
        if ($nodelist->length == 0) {
            throw new AetherNoUrlRuleMatchException("No config entry matched site: $sitename");
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
        $explodedPath = explode('/', substr($path,1));
        /**
         * If AetherSlashMode is "keep", make sure $current is prefixed
         * with a slash as the slash is not maintained from earlier
         */
        if ($this->slashMode() == 'keep') {
            foreach ($explodedPath as $key => $part) {
                $explodedPath[$key] = '/' . $part;
            }
        }
        try {
            $node = $this->findMatchingConfigNode($nodelist, $explodedPath);
        }
        catch (AetherNoUrlRuleMatchException $e) {
            // No match found :(
            throw new Exception("Technical error. No resource found on this url");
        }
        catch (Exception $e) {
            // This is expected
        }
    }
    
    /**
     * Get all modules that are mentioned in this config file
     *
     * @access public
     * @return array
     */
    public function listUsedModules() {
        $doc = new DOMDocument;
        $doc->preserveWhiteSpace = false;
        $doc->load($this->configFilePath);
        $this->doc = $doc;
        $xpath = new DOMXPath($doc);
        $xquery = "//module";
        $modules = $xpath->query($xquery);
        // Find searchpath
        $xpath = new DOMXPath($doc);
        $xquery = "//option[@name='searchpath']";
        $searchPath = trim($xpath->query($xquery)->item(0)->firstChild->nodeValue);
        $searchPath = str_replace(" ", "", $searchPath);

        // If missing ending slash in searchpath, add it!
        if (substr($searchPath, -1) != '/')
            $searchPath .= '/';

        //AetherModuleFactory::$path = $searchPath;
        // Build array
        $modMap = array('start'=>array(),'run'=>array(),
            'stop'=>array(),'missing'=>array());
        // Keep track of not found modules
        $notFound = array();
        foreach ($modules as $node) {
            foreach ($node->childNodes as $option) {
                if ($option->nodeName == '#text')
                    $name = trim($option->nodeValue);
            }
            // Load modules file so we can examine the module
            //$name = 'AetherModule' . $name;
            if (!strpos($searchPath, ';'))
                $paths = array($searchPath);
            else {
                $paths = array_map('trim', explode(';', $searchPath));
            }
            $found = false;
            foreach ($paths as $path) {
                $file = $path . 'modules/' . trim($name) . '.php';
                if (file_exists($file)) {
                    include_once($file);
                    $found = true;
                    break;
                }
            }
            /**
             * Make sure module file actually is found before trying
             * to check its methods
             */
            if ($found) {
                $methods = get_class_methods($name);
                if (is_array($methods)) {
                    if (in_array('start', $methods))
                        $modMap['start'][$name] = $name;
                    if (in_array('run', $methods))
                        $modMap['run'][$name] = $name;
                    if (in_array('stop', $methods))
                        $modMap['stop'][$name] = $name;
                }
            }
            else {
                $notFound[] = trim($name);
                // No need to track dupes
                $notFound = array_unique($notFound);
            }
        }
        $modMap['searchPath'] = $paths;
        $modMap['missing'] = $notFound;
        return $modMap;
    }

    /**
     * Find matching config node from nodelist and path
     *
     * @access private
     * @return node
     * @param DOMNodeList $list
     * @param array $path the/path/sliced into an array(the,path,sliced).
     * if AetherSlashMode is "keep", then "/fragment" will be used, 
     * else "fragment" will be used when matching nodes.
     */
    private function findMatchingConfigNode($nodeList, $path) {
        $match = false;
        // Find first non empty part in path
        $current = "";
        $last = false;
        if (is_array($path) AND count($path) > 0) {
            $current = $path[0];
            $path = array_slice($path, 1);
            if (count($path) == 0)
                $last = true;
        }
        else
            $last = true;
        // Crawl the config hierarchy till the right node is found
        foreach ($nodeList as $node) {
            // This have to be a DOMElement
            if ($node instanceof DOMElement and $node->nodeName == 'rule') {
                /**
                 * When a matching rule is found,
                 * search this level for a default rule ahead
                 * incase no matching rule is found.
                 */
                if ($node->hasAttribute('default')) {
                    $this->defaultRule = $node;
                }
                /* If the attribute match is not set theres no point
                 * in searching through this path
                 * Check if this actually matches the current part
                 * of the path we are examining ($current)
                 */
                if ($this->matches($current, $node)) {
                    $match = $node;
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
                        // Search children for next match
                        try {
                            $match = $this->findMatchingConfigNode(
                                $node->childNodes, $path);
                        }
                        catch (Exception $e) {
                            throw new Exception('Final matching rule found');
                            break;
                        }
                    }
                    else {
                        /**
                         * No children found for rule
                         */
                        $this->subtractNodeConfiguration($node);
                        $this->path = $path;
                        /**
                         * If this is the last fragment
                         * and a match, throw an exception to notify
                         * that further searching can be stopped
                         */
                        if ($last) {
                            throw new Exception('Final matching rule found');
                        }
                    }
                }
            }
        }
        
        /**
         * If this is the last fragment of $path AND
         * a matching node is found, return that node
         */
        if ($last AND $match instanceof DOMElement) {
            return $match;
        }
        /**
         * No rules matched so far, use default rule if one exists
         */
        if ($this->defaultRule !== false) {
            $this->subtractNodeConfiguration($this->defaultRule);
            $this->path = $path;
            return $this->defaultRule;
        }

        /**
         * If this is not the last rule and we have gotten this far it
         * means:
         * No matching node is found
         * No default rule is found
         * So the search should continue until the last rule is processed
         */
        if (!$last)
            return false;
        /**
         * If we reach this point it means NO rules truly matched
         * not even a default rule. Damn bastard developer who doesnt
         * provide a default rule in your app!!!
         */
        if (count($path) > 0) {
            throw new AetherNoUrlRuleMatchException(
                "No rules matches this url. App.config error");
        }
        return false;
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
        $matches = false;
        if ($node->hasAttribute('match')) {
            $matches = ($node->getAttribute('match') == $check);
            $store = $check;
        }
        elseif ($node->hasAttribute('pattern')) {
            $matches = preg_match(
                $node->getAttribute('pattern'), $check, $captures);
            /**
             * When using pattern based matching make sure we store
             * the last matching part of the array of regex matches
             */
            if (is_array($captures))
                $store = array_pop($captures);
        }
        if ($matches) {
            // Store value of url fragment, typical stores and id
            if ($node->hasAttribute('store') AND isset($store)) {
                $this->storeVariable(
                    $node->getAttribute('store'), $store);
            }
            // Remember the url base if this is it
            if ($node->hasAttribute('isBase'))
                $this->urlBase .= $check.'/';
            if ($node->hasAttribute('isRoot'))
                $this->urlRoot .= $check.'/';
            return true;
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
                        if ($child->hasAttribute('cacheas'))
                            $module['cacheas'] = $child->getAttribute('cacheas');
                    }
                    /**
                     * A module could provide itself under a fake name
                     * For example AmobilHeader could be provided as
                     * "header", this object would then be usable
                     * elsewhere in the application
                     */
                    if ($child->hasAttribute('provides'))
                        $module['provides'] = trim($child->getAttribute('provides'));

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
     * @param array $defaults Provide a set of defaults to use if no value is set
     */
    public function getOptions($defaults=array()) {
        return array_merge($defaults, $this->options);
    }

    /**
     * Set and/or change an option
     *
     * Ex: use it to change or add a config option from within a section
     *
     * @access public
     */
    
    public function setOption($name, $value) {
        $this->options[$name] = $value;
    }

    /**
     * Get an array of all urlVariables set.
     * These are the variables in a regex url ex. /ads/([0-9]+)/images
     * which are stored with the store="name"-attribute
     */
    public function getUrlVars() {
        return $this->urlVariables;
    }
    
    /**
     * Get an url variable.
     * These are the variables in a regex url ex. /ads/([0-9]+)/images
     * which are stored with the store="name"-attribute
     */
    public function getUrlVar($key) {
        if ($this->hasUrlVar($key)) 
            return $this->urlVariables[$key];
        else
            throw new Exception("[$key] is not an existing variable");
    }
    
    /**
     * Check if url var exists
     *
     * @return bool
     * @param string $key
     */
    public function hasUrlVar($key) {
        return array_key_exists($key, $this->urlVariables);
    }

    /**
     * Get an url variable (DEPRECATED: use getUrlVar())
     *
     * @access public
     * @return mixed
     * @param string $key
     */
    public function getUrlVariable($key) {
        return $this->getUrlVar($key);
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
    public function getRoot() {
        return $this->urlRoot;
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
     * What slashmode are Aether running in
     *
     * @access public
     * @return string
     */
    public function slashMode() {
        $opts = $this->getOptions();
        if (isset($opts['AetherSlashMode'])) 
            $this->slashMode = $opts['AetherSlashMode'];

        return $this->slashMode;
    }
    
    /**
     * Get configuration file path
     *
     * @access public
     * @return string
     */
    public function configFilePath() {
        return $this->configFilePath;
    }
}
