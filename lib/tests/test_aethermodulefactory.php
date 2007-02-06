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
            new AetherServiceLocator);
        $this->assertIsA($mod, 'AetherModule');
    }
}

if (testRunMode(__FILE__) == SINGLE) {
    $test = new testAetherModuleFactory();
    $test->run($reporter);
}
?>