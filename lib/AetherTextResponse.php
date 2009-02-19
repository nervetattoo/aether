<?php
/*
HARDWARE.NO EDITORSETTINGS:
vim:set tabstop=4:
vim:set shiftwidth=4:
vim:set smarttab:
vim:set expandtab:
*/

require_once('/home/lib/libDefines.lib.php');
require_once(AETHER_PATH . 'lib/AetherResponse.php');

/**
 * 
 * Textual response
 * 
 * Created: 2007-02-05
 * @author Raymond Julin
 * @package aether.lib
 */

class AetherTextResponse extends AetherResponse {
    
    /**
     * Hold text string for output
     * @var string
     */
    private $out = '';
    
    /**
     * Constructor
     *
     * @access public
     * @return AetherTextResponse
     * @param string $output
     */
    public function __construct($output) {
        $this->out = $output;
    }
    
    /**
     * Draw text response. Echoes out the response
     *
     * @access public
     * @return void
     * @param AetherServiceLocator $sl
     */
    public function draw($sl) {
        if (!empty(session_id()))
            $_SESSION['wasGoingTo'] = $_SESSION['REQUEST_URI'];
        try {
            // Timer
            $timer = $sl->get('timer');
            $timer->timerEnd('aether_main');
            // Replace into out content
            $tpl = $sl->getTemplate(98);
            $tpl->selectTemplate('debugBar');
            $timers = $timer->getAllTimers();
            foreach ($timers as $key => $tr) {
                foreach ($tr as $k => $t) {
                    if (!array_key_exists('elapsed', $t))
                        $t['elapsed'] = 0;
                    $timers[$key][$k]['elapsed'] = number_format($t['elapsed'], 4);
                }
            }
            $tpl->setVar('timers', $timers);
            $out = $tpl->returnPage();
            $out = str_replace(
                "<!--INSERTIONPOINT-->",
                $out, $this->out);
        }
        catch (Exception $e) {
            // No timing, we're in prod
            $out = $this->out;
        }
        echo $out;
    }
    
    /**
     * Return instead of echo
     *
     * @access public
     * @return string
     */
    public function get() {
        return $this->out;
    }
}
?>
