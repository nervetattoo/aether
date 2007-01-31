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
     * Get a database connection. If a connection to the desired database
     * allready exists, return it. Else create a new and store it
     *
     * @access public
     * @return Database
     * @param string $name
     */
    public function getDatabase($name) {
        if (!array_key_exists($name, $this->databases) OR
            $this->databases[$name]->isValid() === false) {
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
}
?>
