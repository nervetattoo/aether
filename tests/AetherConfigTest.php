<?php // vim:set ts=4 sw=4 et:

require_once('/home/lib/libDefines.lib.php');
require_once('PHPUnit/Framework.php');
require_once(AETHER_PATH . 'lib/AetherConfig.php');
require_once(AETHER_PATH . 'lib/AetherUrlParser.php');
require_once(AETHER_PATH . 'lib/AetherExceptions.php');

/**
 * 
 * Created: 2009-02-17
 * @author Raymond Julin
 * @package aether.test
 */

class AetherConfigTest extends PHPUnit_Framework_TestCase {
    private function getConfig() {
        return new AetherConfig(AETHER_PATH . 'tests/aether.config.xml');
    }

    public function testEnvironment() {
        $this->assertTrue(class_exists('AetherConfig'));
    }
    
    public function testConfigReadDefault() {
        $url = 'http://raw.no/unittest';
        $aetherUrl = new AetherUrlParser;
        $aetherUrl->parse($url);
        $conf = $this->getConfig();
        $conf->matchUrl($aetherUrl);
        $this->assertEquals($conf->getSection(), 'Generic');
    }

    public function testConfigReadDefaultBase() {
        $url = 'http://raw.no/fluff';
        $aetherUrl = new AetherUrlParser;
        $aetherUrl->parse($url);
        $conf = $this->getConfig();
        $conf->matchUrl($aetherUrl);
        $opts = $conf->getOptions();
        $this->assertEquals($conf->getSection(), 'Generic');
        $this->assertEquals($opts['foobar'], 'yes');
    }

    public function testConfigAssembleOptionsCorrectly() {
        $url = 'http://raw.no/unittest/foo';
        $aetherUrl = new AetherUrlParser;
        $aetherUrl->parse($url);
        $conf = $this->getConfig();
        $conf->matchUrl($aetherUrl);
        $modules = $conf->getModules();
        $module = $modules[0];
        // Check options against the first module
        $this->assertEquals($module['options']['foo'], 'foobar');
        $this->assertEquals($module['options']['bar'], 'foo');
    }

    public function testMultipleModulesOfSameType() {
        $url = 'http://raw.no/tema/Playstation 3';
        $aetherUrl = new AetherUrlParser;
        $aetherUrl->parse($url);
        $conf = $this->getConfig();
        $conf->matchUrl($aetherUrl);
        $modules = $conf->getModules();
        // Check options against the first module
        $this->assertTrue(is_array($modules));
    }

    public function testConfigFindParentDefault() {
        $aetherUrl = new AetherUrlParser;
        // Second
        $url = 'http://raw.no/thisshouldgive404';
        $aetherUrl->parse($url);
        $conf = $this->getConfig();
        $conf->matchUrl($aetherUrl);
        $opts = $conf->getOptions();
        $this->assertEquals($opts['foobar'], 'yes');
        // Third
        $url = 'http://raw.no/unittest/heisann00';
        $aetherUrl->parse($url);
        $conf = $this->getConfig();
        $conf->matchUrl($aetherUrl);
        $opts = $conf->getOptions();
        $this->assertEquals($opts['def'], 'yes');
    }

    public function testConfigFallbackToRootWhenOneMatchEmpty() {
        $aetherUrl = new AetherUrlParser;
        $url = 'http://raw.no/empty/fluff';
        $aetherUrl->parse($url);
        $conf = $this->getConfig();
        $conf->matchUrl($aetherUrl);
        $opts = $conf->getOptions();
        $this->assertEquals($opts['foobar'], 'yes');
    }
    
    public function testConfigFallbackToRootDefault() {
        $aetherUrl = new AetherUrlParser;
        $url = 'http://raw.no/bar/foo/bar';
        $aetherUrl->parse($url);
        $conf = $this->getConfig();
        $conf->matchUrl($aetherUrl);
        $opts = $conf->getOptions();
        $this->assertEquals($opts['foobar'], 'yes');
    }

    public function testMatchWithPlusInItWorks() {
        $aetherUrl = new AetherUrlParser;
        $url = 'http://raw.no/unittest/foo/a+b';
        $aetherUrl->parse($url);
        $conf = $this->getConfig();
        $conf->matchUrl($aetherUrl);
        $opts = $conf->getOptions();
        $this->assertEquals($opts['plusm'], 'yes');
    }

    public function testMatchWithMinusInItWorks() {
        $aetherUrl = new AetherUrlParser;
        $cat = "hifi-produkter";
        $url = 'http://raw.no/unittest/' . $cat;
        $aetherUrl->parse($url);
        $conf = $this->getConfig();
        $conf->matchUrl($aetherUrl);
        $this->assertEquals($conf->getUrlVariable('catName'), $cat);
    }
}
?>
