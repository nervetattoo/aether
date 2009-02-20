<?php // vim:set ts=4 sw=4 et:

require_once('/home/lib/libDefines.lib.php');
require_once(LIB_PATH . 'simpletest.php');
require_once(AETHER_PATH . 'lib/AetherJSONResponse.php');

class testAetherJsonResponse extends UnitTestCase {
    public function testEnvironment() {
        $this->assertTrue(class_exists('AetherJSONResponse'));
    }
    
    public function testResponse() {
        $struct = array('foo'=>'bar',' bar'=>'foo');
        $res = new AetherJSONResponse($struct);
        $out = $res->get();
        $this->assertTrue(strpos($out, '{"foo":"bar"," bar":"foo"}')!==false);
        $this->assertFalse(strpos($out, '*/')!==false);
    }
}

if (testRunMode(__FILE__) == SINGLE) {
    $test = new testAetherJsonResponse();
    $test->run($reporter);
}
?>
