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
require_once(AETHER_PATH . 'lib/AetherExceptions.php');

class testAetherConfig extends UnitTestCase {
    public function testEnvironment() {
        $this->assertTrue(class_exists('AetherConfig'));
    }
    
    public function testConfigReadDefault() {
        $url = 'http://raw.no/unittest';
        $aetherUrl = new AetherUrlParser;
        $aetherUrl->parse($url);
        $conf = new AetherConfig($aetherUrl, AETHER_PATH . 'lib/tests/aether.config.xml');
        $this->assertEqual($conf->getSection(), 'Generic');
    }

    public function testConfigReadDefaultBase() {
        $url = 'http://raw.no/fluff';
        $aetherUrl = new AetherUrlParser;
        $aetherUrl->parse($url);
        $conf = new AetherConfig($aetherUrl, AETHER_PATH . 'lib/tests/aether.config.xml');
        $opts = $conf->getOptions();
        $this->assertEqual($conf->getSection(), 'Generic');
        $this->assertEqual($opts['foobar'], 'yes');
    }

    public function testConfigAssembleOptionsCorrectly() {
        $url = 'http://raw.no/unittest/foo';
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
        $url = 'http://raw.no/tema/Playstation 3';
        $aetherUrl = new AetherUrlParser;
        $aetherUrl->parse($url);
        $conf = new AetherConfig($aetherUrl, AETHER_PATH . 'lib/tests/aether.config.xml');
        $modules = $conf->getModules();
        // Check options against the first module
        $this->assertTrue(is_array($modules));
    }

    public function testConfigFindParentDefault() {
        $aetherUrl = new AetherUrlParser;
        // Second
        $url = 'http://raw.no/thisshouldgive404';
        $aetherUrl->parse($url);
        $conf = new AetherConfig($aetherUrl, AETHER_PATH . 'lib/tests/aether.config.xml');
        $opts = $conf->getOptions();
        $this->assertEqual($opts['foobar'], 'yes');
        // Third
        $url = 'http://raw.no/unittest/heisann00';
        $aetherUrl->parse($url);
        $conf = new AetherConfig($aetherUrl, AETHER_PATH . 'lib/tests/aether.config.xml');
        $opts = $conf->getOptions();
        $this->assertEqual($opts['def'], 'yes');
    }

    public function testConfigFallbackToRootWhenOneMatchEmpty() {
        $aetherUrl = new AetherUrlParser;
        $url = 'http://raw.no/empty/fluff';
        $aetherUrl->parse($url);
        $conf = new AetherConfig($aetherUrl, AETHER_PATH . 'lib/tests/aether.config.xml');
        $opts = $conf->getOptions();
        $this->assertEqual($opts['foobar'], 'yes');
    }
    
    public function testConfigFallbackToRootDefault() {
        $aetherUrl = new AetherUrlParser;
        $url = 'http://raw.no/bar/foo/bar';
        $aetherUrl->parse($url);
        $conf = new AetherConfig($aetherUrl, AETHER_PATH . 'lib/tests/aether.config.xml');
        $opts = $conf->getOptions();
        $this->assertEqual($opts['foobar'], 'yes');
    }

    public function testMatchWithPlusInItWorks() {
        $aetherUrl = new AetherUrlParser;
        $url = 'http://raw.no/unittest/foo/a+b';
        $aetherUrl->parse($url);
        $conf = new AetherConfig($aetherUrl, AETHER_PATH . 'lib/tests/aether.config.xml');
        $opts = $conf->getOptions();
        $this->assertEqual($opts['plusm'], 'yes');
    }

    public function testMatchWithMinusInItWorks() {
        $aetherUrl = new AetherUrlParser;
        $cat = "hifi-produkter";
        $url = 'http://raw.no/unittest/' . $cat;
        $aetherUrl->parse($url);
        $conf = new AetherConfig($aetherUrl, AETHER_PATH . 'lib/tests/aether.config.xml');
        $this->assertEqual($conf->getUrlVariable('catName'), $cat);
    }
}

if (testRunMode(__FILE__) == SINGLE) {
    $test = new testAetherConfig();
    $test->run($reporter);
}
?>
