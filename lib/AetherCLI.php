<?php // vim:set ts=4 sw=4 et:

/**
 * Base class for command line scripting in Aether
 * Sorts out command line option handling for you
 * as this can be a bit cumbersome to do manually each time
 * 
 * Created: 2008-12-12
 * @author Raymond Julin
 * @package aether
 */

class AetherCLI {
    
    /**
     * Define legal options for this program.
     * All other passed options will be ignored
     * Options are supplied on this form:
     * array('shortName' => 'longName');
     * Example:
     * array('a' => 'action', 'p' => 'path');
     * @var array
     */
    protected $allowedOptions = array();
    
    /**
     * Hold all parsed options
     * @var array
     */
    protected $options = array();
    
    /**
     * Handle option parsing
     *
     * @access 
     * @return 
     */
    public function __construct() {
        $this->options = $this->parseOptions($argv);
    }
    
    /**
     * Parse options from command line
     * Only defined options from allowedOptions will be taken into
     * consideration. All others supplied info will be overlooked
     *
     * @access private
     * @return array
     */
    protected function parseOptions($args) {
        if (is_array($args) AND count($args) > 0) {
            $options = array();
            foreach ($args as $arg) {
                // Is valid option
                if (preg_match('/(--[a-z]+|-[a-z]{1})=[a-z-_\/]+/', $arg))  {
                    $parts = explode('=', $arg);
                    $name = preg_replace('/[-]{1,2}/', '', $parts[0]);

                    // Always use long name in returned array
                    if (strlen($name) == 1)
                        $name = $this->allowedOptions[$name];

                    // Only use allowed options
                    if (in_array($name, $this->allowedOptions))
                        $options[$name] = $parts[1];
                }
            }
        }
        return $options;
    }
    
    /**
     * Return a single options value by name
     *
     * @access protected
     * @return mixed
     * @param string $key
     */
    protected function getOption($key) {
        if (array_key_exists($key, $this->options))
            return $this->options[$key];
    }
}
?>
