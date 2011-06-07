<?php // vim:set ts=4 sw=4 et:
/**
 * 
 * Provide a timing and measuring system to aether.
 * Very crude for now
 * 
 * Created: 2009-09-29
 * @author Raymond Julin
 * @package aether
 */

class AetherTimer {
    private $inspectPoints = array();
    private $timers =array();
    private $lastTime = 0;
    private $lastMem = 0;
    /**
     * Get a microtime representation of the moment right here and now
     * @access public
     * @return float seconds and microseconds
     */ 
    function getMicroTime() { 
        list($usec, $sec) = explode(" ", microtime()); 
        return ((float)$usec + (float)$sec); 
    }
    /**
     * Methods for doing timer operations
     *
     * @access public
     * @return void
     * @param string $name
     */
    public function timerStart($name) {
        return $this->start($name);
    }
    public function start($name) {
        $time = $this->getMicroTime();
        $this->timers[$name] = array();
        $this->timers[$name]['start'] = array(
            'time' => $time,
            'memory' => memory_get_usage()
        );
    }
    public function timerEnd($name) {
        $this->end($name);
    }
    public function end($name) {
        $time = $this->getMicroTime();
        $ranFor = $time - $this->timers[$name]['start']['time'];
        $mem = memory_get_usage() - $this->timers[$name]['start']['memory'];
        //$mem = memory_get_usage();
        $this->timers[$name]['total'] = array(
            'time' => $time,
            'memory' => $mem,
            'elapsed' => $ranFor
        );
    }
    public function timerTick($name, $point) {
        $this->tick($name, $point);
    }
    public function tick($name, $point) {
        $time = $this->getMicroTime();
        $mem = memory_get_usage();
        $lastTime = current(array_slice($this->timers[$name],-1,1));
        $this->timers[$name][$point] = array(
            'time' => $time,
            'memory' => $mem,
            'elapsed' => $time - $lastTime['time']);
    }
    public function all() {
        return $this->timers;
    }
}
