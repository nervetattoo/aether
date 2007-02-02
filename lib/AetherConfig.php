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
                /* If the attribute match is not set its no point
                 * in searching through this path
                 * Check if this actually matches the current part
                 * of the path we are examining ($current)
                 */
                if ($node->hasAttribute('match') AND
                    !empty($current) AND
                    $node->getAttribute('match') == $current) {
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
        foreach ($node->childNodes as $child) {
            if ($child instanceof DOMText)
                continue;
            switch ($child->nodeName) {
                case 'section': 
                    $this->section = $child->nodeValue;
                    break;
                case 'subsection':
                    $this->subsection = $child->nodeValue;
                    break;
            }
        }
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
     * Get subsection
     *
     * @access public
     * @return string
     */
    public function getSubSection() {
        return $this->subsection;
    }
}
?>
