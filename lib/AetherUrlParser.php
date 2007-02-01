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
 * Parse an url and make its parts available in an OO way
 * 
 * Created: 2007-02-01
 * @author Raymond Julin
 * @package aether
 */

class AetherUrlParser {
    
    /**
     * Scheme for url
     * @var string
     */
    private $scheme;
    
    /**
     * Host for url
     * @var string
     */
    private $host;
    
    /**
     * Port for url
     * @var int
     */
    private $port;
    
    /**
     * User (if any)
     * @var string
     */
    private $user;
    
    /**
     * Password for user
     * @var string
     */
    private $pass;
    
    /**
     * The path requested
     * @var string
     */
    private $path;
    
    /**
     * Parse an url
     *
     * @access public
     * @return void
     * @param string $url
     */
    public function parse($url) {
        if (!empty($url)) {
            $parts = parse_url($url);
            foreach ($parts as $part => $value) {
                if (property_exists($this, $part))
                    $this->$part = $value;
            }
        }
    }
    
    /**
     * Fetch an url part
     *
     * @access public
     * @return mixed
     * @param string $part
     */
    public function get($part) {
        if (property_exists($this, $part))
            return $this->$part;
        else
            throw new OutOfRangeException("[$part] is not a valid url part");
    }
}
?>
