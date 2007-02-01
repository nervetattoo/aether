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
        $doc->preserveWhiteSPace = false;
        $doc->Load($configFilePath);
        $xpath = new DOMXPath($doc);


        /*
         * Iterate over all parts of url, creating an increasingly more specific
         * xpath query, on the way run this query, and if succesfull, continue
         * running over the parts. Once a failure happens, stop as we
         * have the closest matching node
         */
        $xquery = "//config/site[@name='" . $url->get('host') . "']";
        $xquery .= '/urlRules';

        $path = $url->get('path');
        $parts = explode('/', $path);
        $failedParts = array();
        foreach ($parts as $key => $part) {
            if (!empty($part)) {
                $query = $xquery . "/rule[@match='$part']";
                // Extract the node with the path accumulated till now
                $node = $xpath->query($query);
                /* If this extract is a nodelist with nodes in it,
                 * try next element in the list of parts aswell
                 */
                if ($node instanceof DOMNodeList AND $node->length > 0) {
                    $xquery = $query;
                    $body = $node;
                }
                else {
                    // All failures will be recorded
                    $failedParts[] = $part;
                }
            }
        }
        /*
        echo $xquery;
        print_r($failedParts);
        print_r($body);
        
        foreach ($body as $rule) {
            echo $rule->nodeValue;
        }
        */
        // Read config and find matching 
    }
}
?>
