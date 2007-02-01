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

    public function testParseServerArray() {
        $server = array(
            'HTTP_HOST' => 'aether.raymond.raw.no',
            'SERVER_NAME' => 'aether.raymond.raw.no',
            'SERVER_PORT' => 80,
            'AUTH_TYPE' => 'Basic',
            'GATEWAY_INTERFACE' => 'CGI/1.1',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/',
            'SCRIPT_NAME' => '/deployer.php',
            'PHP_SELF' => '/deployer.php',
            'REQUEST_TIME' => 1170332549
        );
        $parser = new AetherUrlParser;
        $parser->parseServerArray($server);
        $this->assertEqual($parser->get('scheme'), 'http');
        $this->assertEqual($parser->get('path'), '/deployer.php');

        $server['PHP_AUTH_USER'] = 'foo';
        $server['PHP_AUTH_PW'] = 'bar';
        $parser->parseServerArray($server);
        $this->assertEqual($parser->get('user'), 'foo');
        $this->assertEqual($parser->get('pass'), 'bar');
    }
}

if (testRunMode(__FILE__) == SINGLE) {
    $test = new testAetherUrlParser();
    $test->run($reporter);
}
?>
