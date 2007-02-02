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

        /*
        $path = $url->get('path');
        $parts = explode('/', $path);
        $failedParts = array();
        // Start itreation
        $i = 0;
        foreach ($parts as $key => $part) {
            if (!empty($part)) {
                $i++;
                $query = $xquery . "/rule[@match='$part']";
                // Extract the node with the path accumulated till now
                $node = $xpath->query($query);
                */
                /* If this extract is a nodelist with nodes in it,
                 * try next element in the list of parts aswell
                 */
        /*
                if ($node instanceof DOMNodeList AND $node->length > 0) {
                    $xquery = $query;
                    $body = $node;
                }
                else {
                    if ($i == 1) {
                        // If failure happens on first run
                    }
                    // All failures will be recorded
                    $failedParts[] = $part;
                }
            }
        }
        echo $xquery;
        */

        /* We now have a node list containing the closest matches
         * for the xpath query we performed. There are two possible
         * actions to take at this stage:
         * 1. Our nodelist consists of <rule>-s, in which case we need
         *    to find the default rule and apply that one.
         * 2. The nodelist is the final rule, and have <section>
         *    and <subsection> and possibly other config tags
         *    in which case we simply read these out and consider it
         *    done.
         */
        /*
        $first = $body->item(0);
        if ($first->childNodes->length > 1 AND $first->childNodes->item(0)->nodeName == 'rule') {
            // Find the default rule
            $xquery = "rule[@default]";
            $node = $xpath->query($xquery, $first);
            if ($node->length == 1)
                $this->subtractNodeConfiguration($node->item(0));
        }
        else {
            $this->subtractNodeConfiguration($body->item(0));
        }
        */
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
