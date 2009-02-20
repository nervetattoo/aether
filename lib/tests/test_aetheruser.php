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
require_once(AETHER_PATH . 'lib/AetherUser.php');

class testAetherUser extends UnitTestCase {
    public function testEnvironment() {
        $this->assertTrue(class_exists('AetherUser'));
    }

    public function testUser() {
        $user = new AetherUser(new AetherServiceLocator, 18550);
        $this->assertIsA($user, 'AetherUser');
        $this->assertEqual($user->get('givenName'), 'Raymond');
    }
}

if (testRunMode(__FILE__) == SINGLE) {
    $test = new testAetherUser();
    $test->run($reporter);
}
?>
