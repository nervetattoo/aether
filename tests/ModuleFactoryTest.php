<?php // vim:set ts=4 sw=4 et:

require_once('/home/lib/libDefines.lib.php');
require_once(AETHER_PATH . 'lib/AetherModuleFactory.php');
require_once(AETHER_PATH . 'lib/AetherServiceLocator.php');

/**
 * 
 * Created: 2009-02-17
 * @author Raymond Julin
 * @package aether.test
 */

class AetherModuleFactoryTest extends PHPUnit_Framework_TestCase {
    public function testEnvironment() {
        $this->assertTrue(class_exists('AetherModuleFactory'));
    }

    public function testCreate() {
        AetherModuleFactory::$strict = true;
        $mod = AetherModuleFactory::create('Helloworld', 
            new AetherServiceLocator,array('foo'=>'bar'));
        $this->assertEquals($mod->run(), 'Hello world');
    }

    public function testCreateModuleFromCustomFolder() {
        $dir = dirname(__FILE__) . '/';
        AetherModuleFactory::$strict = false;
        AetherModuleFactory::$path = $dir;
        $mod = AetherModuleFactory::create('Hellolocal', 
            new AetherServiceLocator,array('foo'=>'bar'));
        $this->assertEquals($mod->run(), 'Hello local');
    }
}
