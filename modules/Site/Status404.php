<?php
class Status404 extends AetherModule {
    public function run() {
        $a = new AetherActionResponse(404);
        $a->draw($this->sl);
    }
}
