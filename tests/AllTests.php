<?php // vim:set ts=4 sw=4 et:
$path = split("/",pathinfo(__FILE__, PATHINFO_DIRNAME));
array_pop($path);
$path = join("/", $path) . "/";
require_once($path . "Aether.php");
Aether::$aetherPath = $path;
spl_autoload_register(array('Aether', 'autoLoad'));

require_once($path . 'tests/CLITest.php');
require_once($path . 'tests/ConfigTest.php');
require_once($path . 'tests/JsonCommentFilteredResponseTest.php');
require_once($path . 'tests/JsonResponseTest.php');
require_once($path . 'tests/ModuleFactoryTest.php');
require_once($path . 'tests/SectionFactoryTest.php');
require_once($path . 'tests/ServiceLocatorTest.php');
require_once($path . 'tests/UrlParserTest.php');
require_once($path . 'tests/templating/SmartyTest.php');
require_once($path . 'tests/templating/TemplateTest.php');

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
        $suite->addTestSuite('AetherCLITest');
        $suite->addTestSuite('AetherConfigTest');
        $suite->addTestSuite('AetherJsonCommentFilteredResponseTest');
        $suite->addTestSuite('AetherJsonResponseTest');
        $suite->addTestSuite('AetherModuleFactoryTest');
        $suite->addTestSuite('AetherSectionFactoryTest');
        $suite->addTestSuite('AetherServiceLocatorTest');
        $suite->addTestSuite('AetherUrlParserTest');
        $suite->addTestSuite('SmartyIntegratesWithAetherTest');
        $suite->addTestSuite('AetherTemplateTest');
        return $suite;
    }
}
