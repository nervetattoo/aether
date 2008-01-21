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
    public function __construct($statusCode, $data = "") {
        $this->statusCode = $statusCode;
        $this->data = $data;
    }
    
    /**
     * Draw text response. Echoes out the response
     *
     * @access public
     * @return void
     * @param AetherServiceLocator $sl
     */
    public function draw($sl) {
        switch ($this->statusCode) {
            case 301: // Moved permanently
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: {$this->data}");
                break;
            case 302: // Moved temporarily
                header("HTTP/1.1 302 Found");
                header("Location: {$this->data}");
                break;
            case 404: // Not found
                header("HTTP/1.1 404 Not found");
                header("Status: 404 Not found");

                print $this->data;
                break;
        }
    }
    
    /**
     * Return instead of echo
     *
     * @access public
     * @return string
     */
    public function get() {
        return $this->data;
    }
}
?>
