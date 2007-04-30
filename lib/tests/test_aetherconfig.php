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
        $url = 'http://pgfoo.raymond.raw.no/prisguide/data';
        $aetherUrl = new AetherUrlParser;
        $aetherUrl->parse($url);
        $conf = new AetherConfig($aetherUrl, AETHER_PATH . 'lib/tests/aether.config.xml');
        $this->assertEqual($conf->getSection(), 'Priceguide');
        $this->assertEqual($conf->getTemplate(), 
            array('setId'=>98,'name' => 'prisguide_category'));
        $this->assertIsA($conf->getModules(), 'array');
        $this->assertEqual(count($conf->getModules()), 5);
    }
    
    public function testConfigReadDefault() {
        $url = 'http://pgfoo.raymond.raw.no/prisguide';
        $aetherUrl = new AetherUrlParser;
        $aetherUrl->parse($url);
        $conf = new AetherConfig($aetherUrl, AETHER_PATH . 'lib/tests/aether.config.xml');
        $this->assertEqual($conf->getSection(), 'Priceguide');
    }

    public function testConfigReadDefaultBase() {
        $url = 'http://pgfoo.raymond.raw.no/fluff';
        $aetherUrl = new AetherUrlParser;
        $aetherUrl->parse($url);
        $conf = new AetherConfig($aetherUrl, AETHER_PATH . 'lib/tests/aether.config.xml');
        $this->assertEqual($conf->getSection(), 'Generic');
    }

    public function testConfigAssembleOptionsCorrectly() {
        $url = 'http://pgfoo.raymond.raw.no/unittest/foo';
        $aetherUrl = new AetherUrlParser;
        $aetherUrl->parse($url);
        $conf = new AetherConfig($aetherUrl, AETHER_PATH . 'lib/tests/aether.config.xml');
        $modules = $conf->getModules();
        $module = $modules[0];
        // Check options against the first module
        $this->assertEqual($module['options']['foo'], 'foobar');
        $this->assertEqual($module['options']['bar'], 'foo');
    }

    public function testMultipleModulesOfSameType() {
        $url = 'http://pgfoo.raymond.raw.no/tema/Playstation 3';
        $aetherUrl = new AetherUrlParser;
        $aetherUrl->parse($url);
        $conf = new AetherConfig($aetherUrl, AETHER_PATH . 'lib/tests/aether.config.xml');
        $modules = $conf->getModules();
        // Check options against the first module
        $this->assertTrue(is_array($modules));
    }
}

if (testRunMode(__FILE__) == SINGLE) {
    $test = new testAetherConfig();
    $test->run($reporter);
}
?>
