<?php
class Status404 extends AetherModule {
    public function run() {
        $msg = '<h2>Err 404!</h2>';
        $a = new AetherActionResponse(404, $msg);
        return $a->draw($this->sl);
    }
}
