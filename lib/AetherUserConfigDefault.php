<?php
/*
HARDWARE.NO EDITORSETTINGS:
vim:set tabstop=4:
vim:set shiftwidth=4:
vim:set smarttab:
vim:set expandtab:
*/

/**
 * Loads all defaults values 
 *
 * Created: 2007-03-06
 * @author Pål-Eivind Johnsen
 */

class AetherUserConfigDefault {

    /**
     * Holds service locator
     * @var AetherServiceLocator
     */
    private $sl;

    /**
     * Holds all the default values
     * @var array
     */
    private $values;

    /**
    * Constructor
    *
    * @param AetherServiceLocator Service Locator
    */
    public function __construct($sl) {
        $this->sl = $sl;
        $this->loadValues();
    }

    /**
     * Loads all defaults values
     *
     * @access private
     * @return array
     */
    private function loadValues() {
        $sql = "SELECT * FROM aether_user_config_defaults";
        $this->values = $this->sl->getDatabase("pubsys")->query($sql);
    }

    /**
     * Returns all defaults values
     *
     * @access public
     * @return array
     */
    public function getValues() {
        return $this->values;
    }
}
?>
