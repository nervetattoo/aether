<?php

/**
 * Aether supports using any caching class you want
 * Interface to implement if you are to provide a caching class
 * into aether.
 *
 * @author Raymond Julin <raymond.julin@gmail.com>
 * @package Aether
 */

interface AetherCacheInterface {
    public function set($name, $data, $ttl=false);
    public function get($name, $maxAge=false);
    public function rm($name);
    public function has($name);
}
