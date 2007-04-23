<?php
/*
HARDWARE.NO EDITORSETTINGS:
vim:set tabstop=4:
vim:set shiftwidth=4:
vim:set smarttab:
vim:set expandtab:
*/

require_once('/home/lib/libDefines.lib.php');
require_once(LIB_PATH . 'Database.lib.php');
require_once(LIB_PATH . 'Template.lib.php');
require_once(LIB_PATH . 'SessionHandler.lib.php');
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
     * Hold database connections
     * @var array
     */
    private $databases = array();
    
    /**
     * Hold template objects
     * @var array
     */
    private $templates = array();
    
    /**
     * Hold custom objects
     * @var array
     */
    private $custom = array();
    
    /**
     * Get a database connection. If a connection to the desired database
     * already exists, return it. Else create a new and store it
     *
     * @access public
     * @return Database
     * @param string $name
     */
    public function getDatabase($name) {
        if (!array_key_exists($name, $this->databases) OR
            $this->databases[$name]->isValid() === false) {
            /*
            if ($name == 'prisguide')
                $name = 'prisguide_new';
            */
            $this->databases[$name] = new Database($name);
        }
        return $this->databases[$name];
    }
    
    /**
     * Get a template object for template set with id.
     * If a template object for this allready exists, return it
     * else create  a new template object and store it
     *
     * @access public
     * @return Template
     * @param int $setId
     */
    public function getTemplate($setId) {
        if (!array_key_exists($setId, $this->templates)) {
            $this->templates[$setId] = new Template($setId);
        }
        return $this->templates[$setId];
    }
    
    /**
     * Get SessionHandler object. Using this implies starting
     * up a session or continuing the existing one
     *
     * @access public
     * @return SessionHandler
     */
    public function getSession() {
        if (!($this->session instanceof SessionHandler))
            $this->session = new SessionHandler;
        return $this->session;
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
