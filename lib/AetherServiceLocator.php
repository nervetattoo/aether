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
require_once(LIB_PATH . 'Vector.php');

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
     * Holds user objects
     * @var array
     */
    private $users = array();
    
    /**
     * Hold custom objects
     * @var array
     */
    private $custom = array();
    
    /**
     * Hold list of vectors
     * @var array
     */
    public $vectors = array();
    
    /**
     * Fetch a user object. If non existing, create it
     *
     * @access public
     * @return AetherUser
     * @param int $userId
     */
    public function getUser($userId) {
        if (array_key_exists($userId, $this->users))
            return $this->users[$userId];
        else
            return $this->templates[$userId] = new AetherUser($this, $userId);
    }
    
    /**
     * Returns a reference to a template object
     * This is defined in the base class, but aether needs
     * to always make sure a special array $aether is
     * put into the mix
     *
     * @access public
     * @return template A template object
     * @param integer $id Template set id
     */
    public function getTemplate($id) {
        if (!array_key_exists($id, $this->templates))
            $this->templates[$id] = new Template($id);
        $tpl = $this->templates[$id];
        // Add global stuff
        $providers = $this->getVector('aetherProviders');
        $tpl->setVar('aether', array_merge(
            array('providers' => $providers),
            $this->getVector('templateGlobals')->getAsArray())
        );
        return $tpl;
    }

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
        return $this->set($name, $object);
    }
    public function set($name, $object) {
        if (!$this->hasObject($name)) {
            // Do not allow saving non objects
            if (is_object($object) || is_array($object)) {
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
        return $this->get($name);
    }
    public function get($name) {
        if ($this->hasObject($name))
            return $this->custom[$name];
        else
            throw new Exception('Custom object ['.$name.'] does not exist');
    }
    
    /**
     * Give access to vector x
     *
     * @access public
     * @return array
     * @param string $name
     */
    public function getVector($name) {
        if (!isset($this->vectors[$name]))
            $this->vectors[$name] = new Vector;
        return $this->vectors[$name];
    }
    
    /**
     * Check if custom object exists
     *
     * @access public
     * @return bool
     * @param string $name
     */
    public function hasCustomObject($name) {
        return $this->hasObject($name);
    }

    public function hasObject($name) {
        if (array_key_exists($name, $this->custom)) {
            return (is_object($this->custom[$name]) || is_array($this->custom[$name]));
        }
        return false;
    }
}
?>
