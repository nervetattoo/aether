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
require_once(getFilepath(__FILE__) . '/../AetherServiceLocator.php');
require_once(getFilepath(__FILE__) . '/../AetherUserConfigDefault.php');

class testAetherUserConfigDefault extends UnitTestCase {
    function testDefaults() {
        $c = new AetherUserConfigDefault(new AetherServiceLocator);
        $this->assertTrue(is_object($c));
        $v = $c->getValues();
        $this->assertFalse(empty($v));
    }
}

if (testRunMode(__FILE__) == SINGLE) {
    $test = new testAetherUserConfigDefault;
    $test->run(new TextReporter());
}

?>
