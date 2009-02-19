<?php // vim:set ts=4 sw=4 et:

require_once('/home/lib/libDefines.lib.php');
require_once('PHPUnit/Framework.php');
require_once(AETHER_PATH . 'lib/AetherSectionFactory.php');
require_once(AETHER_PATH . 'lib/AetherServiceLocator.php');

/**
 * 
 * Created: 2009-02-17
 * @author Raymond Julin
 * @package aether.test
 */

class AetherSectionFactoryTest extends PHPUnit_Framework_TestCase {
    public function testEnvironment() {
        $this->assertTrue(class_exists('AetherSectionFactory'));
    }

    public function testCreate() {
        
        $dir = dirname(__FILE__) . '/';
        AetherSectionFactory::$strict = false;
        AetherSectionFactory::$path = $dir;
        $section = AetherSectionFactory::create('Test', new AetherServiceLocator);
        $this->assertType('AetherSectionTest', $section);
    }
}
?>
