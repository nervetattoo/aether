<?php
/*
HARDWARE.NO EDITORSETTINGS:
vim:set tabstop=4:
vim:set shiftwidth=4:
vim:set smarttab:
vim:set expandtab:
*/

require_once('/home/lib/libDefines.lib.php');
require_once(LIB_PATH . 'Vector.php');
require_once(LIB_PATH . 'Database.lib.php');
require_once(AETHER_PATH . 'lib/templating/AetherTemplate.php');

/**
 * 
 * Aether service locator, an object to locate services needed
 * Gives access to database, template and other common objects
 * 
 * Created: 2007-01-31
 * @author Raymond Julin
 * @package aether
 */

class AetherServiceLocator {
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
     * Hold template object
     * @var object
     */
    private $template = null;

    /**
     * Fetch a reference to the templating object
     * thats floating around in Aether
     *
     * @access public
     * @return AetherTemplate A template object
     */
    public function getTemplate() {
        if ($this->template == null)
            $this->template = AetherTemplate::get('smarty',$this);
        // Add global stuff
        $providers = $this->getVector('aetherProviders');
        $this->template->set('aether', array_merge(
            array('providers' => $providers),
            $this->getVector('templateGlobals')->getAsArray())
        );
        return $this->template;
    }

    /**
     * Returns a reference to a database object
     *
     * @access public
     * @return Database Requested database object
     * @param string $name database name
     */
    public function getDatabase($name) {
        throw new Exception("AetherServiceLocator::getDatabase() is deprecated");
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
    public function set($name, $object) {
        if (!$this->hasObject($name)) {
            $this->custom[$name] = $object;
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

    public function hasObject($name) {
        return array_key_exists($name, $this->custom);
    }
}
?>
