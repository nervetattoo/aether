<?php // vim:set ts=4 sw=4 et:

require_once('/home/lib/libDefines.lib.php');
require_once(LIB_PATH . 'simpletest.php');
require_once(AETHER_PATH . 'lib/AetherJSONCommentFilteredResponse.php');

class testAetherJsonCommentFilteredResponse extends UnitTestCase {
    public function testEnvironment() {
        $this->assertTrue(class_exists('AetherJSONCommentFilteredResponse'));
    }
    
    public function testResponse() {
        $struct = array('foo'=>'bar',' bar'=>'foo');
        $res = new AetherJSONCommentFilteredResponse($struct);
        $out = $res->get();
        $this->assertTrue(strpos($out, '{"foo":"bar"," bar":"foo"}')!==false);
        $this->assertTrue(preg_match('/\/\*[^\*]+\*\//',$out));
    }
}

if (testRunMode(__FILE__) == SINGLE) {
    $test = new testAetherJsonCommentFilteredResponse();
    $test->run($reporter);
}
?>
