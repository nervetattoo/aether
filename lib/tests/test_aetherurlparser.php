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
require_once(AETHER_PATH . 'lib/AetherUrlParser.php');

class testAetherUrlParser extends UnitTestCase {
    public function testEnvironment() {
        $this->assertTrue(class_exists('AetherUrlParser'));
    }
    
    public function testParser() {
        $url = 'http://aether.raymond.raw.no/foobar/hello/';
        $parser = new AetherUrlParser;
        $parser->parse($url);
        $this->assertEqual($parser->get('scheme'), 'http');
        $user = $parser->get('user');
        $this->assertTrue(empty($user));

        $url2 = 'ftp://foo:bar@hw.no/world';
        $parser = new AetherUrlParser;
        $parser->parse($url2);
        $this->assertEqual($parser->get('scheme'), 'ftp');
        $this->assertEqual($parser->get('user'), 'foo');
        $this->assertEqual($parser->get('pass'), 'bar');
        $this->assertEqual($parser->get('path'), '/world');
    }
}

if (testRunMode(__FILE__) == SINGLE) {
    $test = new testAetherUrlParser();
    $test->run($reporter);
}
?>
