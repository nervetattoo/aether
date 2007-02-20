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
require_once(getFilepath(__FILE__) . '/../AetherDefaultConfig.php');

class testAetherDefaultConfig extends UnitTestCase {
    function testSetGetSaveResetIsset() {
        $c = new AetherDefaultConfig(new AetherServicelocator, 90746);
        $c->set("foo", "F00");
        $c->set("bar", "B4R");
        $c->save();
        unset($c);
        $c = new AetherDefaultConfig(new AetherServicelocator, 90746);
        $this->assertEqual($c->get("foo"), "F00");
        $this->assertEqual($c->get("bar"), "B4R");
        $c->set("foo", "Foo");
        $c->set("bar", "Ba'r");
        $c->save();
        unset($c);
        $c = new AetherDefaultConfig(new AetherServicelocator, 90746);
        $this->assertEqual($c->get("foo"), "Foo");
        $this->assertEqual($c->get("bar"), "Ba'r");
        $this->assertEqual($c->isKeySet("foo"), true);
        $c->reset("bar");
        $this->assertEqual($c->isKeySet("foo"), true);
        $this->assertEqual($c->isKeySet("bar"), false);
        $c->reset();
        $this->assertEqual($c->isKeySet("foo"), false);
    }
}


if (testRunMode(__FILE__) == SINGLE) {
    $test = new testAetherDefaultConfig;
    $test->run(new TextReporter());
}

?>
