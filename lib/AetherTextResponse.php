<?php
/*
HARDWARE.NO EDITORSETTINGS:
vim:set tabstop=4:
vim:set shiftwidth=4:
vim:set smarttab:
vim:set expandtab:
*/

require_once('/home/lib/libDefines.lib.php');
require_once(AETHER_PATH . 'lib/AetherResponse.php');

/**
 * 
 * Textual response
 * 
 * Created: 2007-02-05
 * @author Raymond Julin
 * @package aether.lib
 */

class AetherTextResponse extends AetherResponse {
    
    /**
     * Hold text string for output
     * @var string
     */
    private $output = '';
    
    /**
     * Constructor
     *
     * @access public
     * @return AetherTextResponse
     * @param string $output
     */
    public function __construct($output) {
        $this->ouput = $output;
    }
    
    /**
     * Draw text response. Echoes out the response
     *
     * @access public
     * @return void
     */
    public function draw() {
        echo $this->output;
    }
}
?>
