<?php // vim:set ts=4 sw=4 et:

require_once('/home/lib/libDefines.lib.php');
require_once('PHPUnit/Framework.php');
require_once(AETHER_PATH . 'lib/tests/AetherCLITest.php');
require_once(AETHER_PATH . 'lib/tests/AetherConfigTest.php');
require_once(AETHER_PATH . 'lib/tests/AetherJsonCommentFilteredResponseTest.php');
require_once(AETHER_PATH . 'lib/tests/AetherJsonResponseTest.php');
require_once(AETHER_PATH . 'lib/tests/AetherModuleFactoryTest.php');
require_once(AETHER_PATH . 'lib/tests/AetherSectionFactoryTest.php');
require_once(AETHER_PATH . 'lib/tests/AetherServiceLocatorTest.php');
require_once(AETHER_PATH . 'lib/tests/AetherUrlParserTest.php');

/**
 * 
 * Run all PHPUnit test cases for aether
 * 
 * Created: 2009-02-17
 * @author Raymond Julin
 * @package aether.test
 */

class Framework_AllTests {
    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite('Aether Framework');
        $suite->addTestSuite('AetherConfigTest');
        $suite->addTestSuite('AetherJsonCommentFilteredResponseTest');
        $suite->addTestSuite('AetherJsonResponseTest');
        $suite->addTestSuite('AetherModuleFactoryTest');
        $suite->addTestSuite('AetherSectionFactoryTest');
        $suite->addTestSuite('AetherServiceLocatorTest');
        $suite->addTestSuite('AetherUrlParserTest');
        return $suite;
    }
}
?>
