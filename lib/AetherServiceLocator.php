<?php
/*
HARDWARE.NO EDITORSETTINGS:
vim:set tabstop=4:
vim:set shiftwidth=4:
vim:set smarttab:
vim:set expandtab:
*/

require_once('/home/lib/libDefines.lib.php');
require_once(LIB_PATH . 'ServiceLocator.php');
/**
 * 
 * Aether service locator, an object to locate services needed
 * Gives access to database, template and other common objects
 * 
 * Created: 2007-01-31
 * @author Raymond Julin
 * @package aether
 */

class AetherServiceLocator extends ServiceLocator {
    
    /**
     * Hold custom objects
     * @var array
     */
    private $custom = array();

    /**
     * Save a custom object to the service locators storage
     * This functionality is meant for sharing objects between
     * components (Subsection and FooComponent)
     * Only one unique object per name can be held
     *
     * @access public
     * @return void
     * @param string $name Name to use as lookup for object
     * @param object $object The actual object
     */
    public function saveCustomObject($name, $object) {
        if (!$this->hasCustomObject($name)) {
            // Do not allow saving non objects
            if (is_object($object)) {
                $this->custom[$name] = $object;
            }
            else {
                throw new InvalidArgumentException("[$object] is not a valid object");
            }
        }
        else {
            // Throw exception
            throw new Exception('Object name is already in use ['.$name.']');
        }
    }
    
    /**
     * Fetch a custom object
     *
     * @access public
     * @return object
     * @param string $name
     */
    public function fetchCustomObject($name) {
        if ($this->hasCustomObject($name))
            return $this->custom[$name];
        else
            throw new Exception('Custom object ['.$name.'] does not exist');
    }
    
    /**
     * Check if custom object exists
     *
     * @access public
     * @return bool
     * @param string $name
     */
    public function hasCustomObject($name) {
        if (array_key_exists($name, $this->custom)) {
            return (is_object($this->custom[$name]));
        }
        return false;
    }
}
?>
