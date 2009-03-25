<?php // 
require_once('PHPUnit/Framework.php');
require_once('/home/lib/libDefines.lib.php');
require_once(AETHER_PATH . 'tests/AetherCLITest.php');
require_once(AETHER_PATH . 'tests/AetherConfigTest.php');
require_once(AETHER_PATH . 'tests/AetherJsonCommentFilteredResponseTest.php');
require_once(AETHER_PATH . 'tests/AetherJsonResponseTest.php');
require_once(AETHER_PATH . 'tests/AetherModuleFactoryTest.php');
require_once(AETHER_PATH . 'tests/AetherSectionFactoryTest.php');
require_once(AETHER_PATH . 'tests/AetherServiceLocatorTest.php');
require_once(AETHER_PATH . 'tests/AetherUrlParserTest.php');


/**
 * Set up autoloader that will load testcases as they
 * are used
 *
 * @return void
 * @param string $name
 */
/*
function __autoload($name) {
    //Derive base path from this file
    //Support including both the libs and the tests
    if (strpos($name, 'Test') > 1)
        $basePath = dirname(__FILE__) . "/tests/";
    else
        $basePath = dirname(__FILE__) . "/lib/";

    // Final path for file
    $filePath = $basePath . $name . ".php";

    // Require the file
    if (!empty($filePath))
        require_once($filePath);
}
*/

/**
 * 
 * Run all tests under teh tests/ folder, generate error reports and code coverage report
 * 
 * Created: 2009-03-25
 * @author Raymond Julin
 * @package aether
 */

class AllTests {
    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite('Aether');
        //$suite->addTestSuite('AetherCLITest');
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
