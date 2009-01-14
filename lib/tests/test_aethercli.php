<?php // vim:set ts=4 sw=4 et:

require_once('/home/lib/libDefines.lib.php');
require_once(LIB_PATH . 'simpletest.php');
require_once(AETHER_PATH . 'lib/AetherCLI.php');

/**
 * 
 * Test AetherCLI
 * 
 * Created: 2008-12-12
 * @author Raymond Julin
 * @package aether.tests
 */

class TestCLIApp extends AetherCLI {
    protected $allowedOptions = array(
        'f' => 'foo',
        'm' => 'meh'
    );
    public function getOptions($str) {
        $this->options = $this->parseOptions($str);
        return $this->options;
    }
}
class TestCLIApp2 extends TestCLIApp {
    protected $allowedOptions = array();
}

class testAetherCLI extends UnitTestCase {
    public function testParseOptions() {
        $app = new TestCLIApp;

        // Pass #0 Long opts with filename.php
        $opts = $app->getOptions(array("filename.php", "--foo=bar"));
        $arr = array('foo'=>'bar');
        $this->assertEqual($arr, $opts);

        // Pass #1 Long opts
        $opts = $app->getOptions(array("--foo=bar"));
        $arr = array('foo'=>'bar');
        $this->assertEqual($arr, $opts);

        // Pass #2 Long opts
        $opts = $app->getOptions(array("--foo=bar", "--meh=eh"));
        $arr = array('foo'=>'bar','meh'=>'eh');
        $this->assertEqual($arr, $opts);

        // Pass #3 Long opts, illegal option 
        $opts = $app->getOptions(array("--foo=bar", "--bleh=eh"));
        $arr = array('foo'=>'bar');
        $this->assertEqual($arr, $opts);

        // Pass #4 Short opts
        $opts = $app->getOptions(array("-f=bar", "-m=eh"));
        $arr = array('foo'=>'bar', 'meh'=>'eh');
        $this->assertEqual($arr, $opts);

        // Pass #5 Short opts, illegal options
        $opts = $app->getOptions(array("-t=bar", "-m=eh"));
        $arr = array('meh'=>'eh');
        $this->assertEqual($arr, $opts);

        // Pass #6 Fetch option one by one
        $app->getOptions(array("--meh=eh"));
        //$arr = array('meh'=>'eh');
        $this->assertEqual($app->getOption('meh'), 'eh');
    }

    public function testHelpMixin() {
        $app = new TestCLIApp2;
        $this->assertEqual($app->getOption('h'), '');
        $arr = array('help'=>'');
        $opts = $app->getOptions(array("--help"));
        $this->assertEqual($arr, $opts);
    }

    public function testHasOptions() {
        $app = new TestCLIApp;
        $opts = $app->getOptions(array("filename.php", "--foo=bar"));
        $arr = array('foo'=>'bar');
        $this->assertEqual($arr, $opts);
        $this->assertTrue($app->hasOptions(array('foo')));
        $this->assertFalse($app->hasOptions(array('foo','bar')));
    }
}

if (testRunMode(__FILE__) == SINGLE) {
    $test = new testAetherCLI;
    $test->run($reporter);
}
?>
