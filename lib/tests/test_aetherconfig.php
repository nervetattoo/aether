<?php
/*
HARDWARE.NO EDITORSETTINGS:
vim:set tabstop=4:
vim:set shiftwidth=4:
vim:set smarttab:
vim:set expandtab:
*/


require_once('/home/lib/libDefines.lib.php');
require_once(LIB_PATH . 'simpletest.php');
require_once(AETHER_PATH . 'lib/AetherConfig.php');
require_once(AETHER_PATH . 'lib/AetherUrlParser.php');

class testAetherConfig extends UnitTestCase {
    public function testEnvironment() {
        $this->assertTrue(class_exists('AetherConfig'));
    }

    public function testConfigReadExact() {
        $url = 'http://aether.raymond.raw.no/foobar/helloworld';
        $aetherUrl = new AetherUrlParser;
        $aetherUrl->parse($url);
        $conf = new AetherConfig($aetherUrl, AETHER_PATH . 'aether.config.xml');
        $this->assertEqual($conf->getSection(), 'Foobar');
        $this->assertEqual($conf->getSubSection(), 'Hello');
    }
    
    public function testConfigReadDefault() {
        $url = 'http://aether.raymond.raw.no/foobar';
        $aetherUrl = new AetherUrlParser;
        $aetherUrl->parse($url);
        $conf = new AetherConfig($aetherUrl, AETHER_PATH . 'aether.config.xml');
        $this->assertEqual($conf->getSection(), 'Foobar');
        $this->assertEqual($conf->getSubSection(), 'PageNotFound');
    }

    public function testConfigReadDefaultBase() {
        $url = 'http://aether.raymond.raw.no/fluff';
        $aetherUrl = new AetherUrlParser;
        $aetherUrl->parse($url);
        $conf = new AetherConfig($aetherUrl, AETHER_PATH . 'aether.config.xml');
        $this->assertEqual($conf->getSection(), 'Error');
        $this->assertEqual($conf->getSubSection(), 'PageNotFound');
    }
}

if (testRunMode(__FILE__) == SINGLE) {
    $test = new testAetherConfig();
    $test->run($reporter);
}
?>
