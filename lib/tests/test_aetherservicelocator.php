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
require_once(AETHER_PATH . 'lib/AetherServiceLocator.php');

class testAetherServiceLocator extends UnitTestCase {
    public function testEnvironment() {
        $this->assertTrue(class_exists('AetherServiceLocator'));
    }

    public function testGetDatabase() {
        $asl = new AetherServiceLocator;
        $db = $asl->getDatabase('neo');
        $this->assertIsA($db, 'Database');
    }

    public function testGetTemplate() {
        $asl = new AetherServiceLocator;
        $db = $asl->getTemplate(86);
        $this->assertIsA($db, 'Template');
    }
    
    public function testCustomObjectStorage() {
        // Create a small class for testing
        $obj = new stdClass;
        $obj->foo = 'bar';
        $asl = new AetherServiceLocator;
        $asl->saveCustomObject('tester', $obj);
        $tester = $asl->fetchCustomObject('tester');
        $this->assertIdentical($tester, $obj);
    }
}

if (testRunMode(__FILE__) == SINGLE) {
    $test = new testAetherServiceLocator();
    $test->run($reporter);
}
?>
