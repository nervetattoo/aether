<?php
/**
 * 
 * Simple helloworld module, just a test module
 * 
 * Created: 2007-02-06
 * @author Raymond Julin
 * @package aether.module
 */

class AetherModuleHelloworld extends AetherModule {
    
    /**
     * Render module
     *
     * @access public
     * @return string
     */
    public function run() {
        return 'Hello world';
    }
    public function stop() {
        //echo 'Fooooooo';
    }
}
