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
require_once(AETHER_PATH . 'lib/AetherSectionFactory.php');
require_once(AETHER_PATH . 'lib/AetherServiceLocator.php');

class testAetherSectionFactory extends UnitTestCase {
    public function testEnvironment() {
        $this->assertTrue(class_exists('AetherSectionFactory'));
    }

    public function testCreate() {
        
        $section = AetherSectionFactory::create('Priceguide', new AetherServiceLocator);
        $this->assertIsA($section, 'AetherSection');
    }
}

if (testRunMode(__FILE__) == SINGLE) {
    $test = new testAetherSectionFactory();
    $test->run($reporter);
}
?>
