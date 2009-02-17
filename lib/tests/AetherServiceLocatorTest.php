<?php // vim:set ts=4 sw=4 et:

require_once('/home/lib/libDefines.lib.php');
require_once('PHPUnit/Framework.php');
require_once(AETHER_PATH . 'lib/AetherServiceLocator.php');

/**
 * 
 * Created: 2009-02-17
 * @author Raymond Julin
 * @package aether.test
 */

class AetherServiceLocatorTest extends PHPUnit_Framework_TestCase {
    public function testEnvironment() {
        $this->assertTrue(class_exists('AetherServiceLocator'));
    }

    public function testGetDatabase() {
        $asl = new AetherServiceLocator;
        $db = $asl->getDatabase('neo');
        $this->assertType('Database', $db);
    }

    public function testGetTemplate() {
        $asl = new AetherServiceLocator;
        $tpl = $asl->getTemplate(86);
        $this->assertType('Template',$tpl);
    }
    
    public function testCustomObjectStorage() {
        // Create a small class for testing
        $obj = new stdClass;
        $obj->foo = 'bar';
        $asl = new AetherServiceLocator;
        $asl->saveCustomObject('tester', $obj);
        $tester = $asl->fetchCustomObject('tester');
        $this->assertSame($tester, $obj);
    }

    public function testArray() {
        $asl = new AetherServiceLocator;
        $arr = $asl->getVector('foo');
        $arr['foo'] = 'bar';
        $arr2 = $asl->getVector('foo');
        $this->assertEquals($arr['foo'], $arr2['foo']);
    }
}
?>
