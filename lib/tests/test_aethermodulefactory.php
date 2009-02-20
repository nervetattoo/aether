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
require_once(AETHER_PATH . 'lib/AetherModuleFactory.php');
require_once(AETHER_PATH . 'lib/AetherServiceLocator.php');

class testAetherModuleFactory extends UnitTestCase {
    public function testEnvironment() {
        $this->assertTrue(class_exists('AetherModuleFactory'));
    }

    public function testCreate() {
        $mod = AetherModuleFactory::create('Helloworld', 
            new AetherServiceLocator,array('foo'=>'bar'));
        $this->assertEqual($mod->render(), 'Hello world');
    }

    public function testCreateModuleFromCustomFolder() {
        $dir = dirname(__FILE__) . '/';
        AetherModuleFactory::$path = $dir;
        $mod = AetherModuleFactory::create('Hellolocal', 
            new AetherServiceLocator,array('foo'=>'bar'));
        $this->assertEqual($mod->render(), 'Hello local');
    }
}

if (testRunMode(__FILE__) == SINGLE) {
    $test = new testAetherModuleFactory();
    $test->run($reporter);
}
?>
