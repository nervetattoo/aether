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
require_once(AETHER_PATH . 'Aether.php');

class testAether extends UnitTestCase {
    public function testEnvironment() {
        $this->assertTrue(class_exists('Aether'));
    }

    public function testServiceLocator() {
        $aether = new Aether;
        $this->assertIsA($aether->sl, 'AetherServiceLocator');
    }
}

if (testRunMode(__FILE__) == SINGLE) {
    $test = new testAether();
    $test->run($reporter);
}
?>
