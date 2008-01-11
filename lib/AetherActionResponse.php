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
 * Action response
 * 
 * Created: 2007-02-07
 * @author Raymond Julin
 * @package aether.lib
 */

class AetherActionResponse extends AetherResponse {
    
    /**
     * Url to redirect to
     * @var string
     */
    private $url = '';
    
    /**
     * Constructor
     *
     * @access public
     * @return AetherActionResponse
     * @param string $url
     */
    public function __construct($url) {
        $this->url = $url;
    }
    
    /**
     * Draw text response. Echoes out the response
     *
     * @access public
     * @return void
     * @param AetherServiceLocator $sl
     */
    public function draw($sl) {
        header("Location: {$this->url}");
    }
    
    /**
     * Return instead of echo
     *
     * @access public
     * @return string
     */
    public function get() {
        return $this->url;
    }
}
?>
