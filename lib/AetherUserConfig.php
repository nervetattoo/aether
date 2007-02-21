<?php
/*
HARDWARE.NO EDITORSETTINGS:
vim:set tabstop=4:
vim:set shiftwidth=4:
vim:set smarttab:
vim:set expandtab:
*/

/**
 * 
 * Holds config for user
 *
 * Created: 2007-02-20
 * @author Pål-Eivind Johnsen
 */

class AetherUserConfig {
    /**
     * User id
     * @var int
     */
    private $id;

    /**
     * All the key/value pairs for this id
     * @var array
     */
    private $values;

    /**
    * Constructor
    *
    * @param AetherServiceLocator Service Locator
    * @param int the user id
    */
    public function __construct($sl, $id) {
        $this->sl = $sl;
        $this->id = (int)$id;
        $this->loadValues();
    }

    /**
     * Loads all values into the $values-array
     *
     * @return void
     */
    private function loadValues() {
        if (isset($this->values))
            unset($this->values);

        $db = $this->sl->getDatabase("pubsys");
        $q = "  
            SELECT `key`, `value`
            FROM aether_user_config 
            WHERE `id` = {$this->id}";
        $rows = $db->query($q);
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $this->values[$row['key']] = $row['value'];
            }
        }
    }

    /**
     * Returns the value associated with the provided key
     *
     * @param string $key
     * @return string
     */
    public function get($key) {
        return $this->values[$key];
    }

    /**
     * Sets $key to $value
     *
     * @param string key
     * @param string value
     * @return void
     */
    public function set($key, $value) {
        $this->values[$key] = $value;
    }

    /**
     * Checks if $key is set
     *
     * @param string key
     * @return boolean
     */
    public function isKeySet($key) {
        return isset($this->values[$key]);
    }
 
    /**
     * Get keys for all set values
     *
     * @access public
     * @return array
     */
    public function getAllKeys() {
        return array_keys($this->values);
    }
 
    /**
     * Deletes config [for $key]
     *
     * @param string key=false
     * @return void
     */
    public function reset($key = false) {
        $db = $this->sl->getDatabase("pubsys");
        if ($key == false) {
            $q = "DELETE FROM aether_user_config
                WHERE `id` = '{$this->id}'";
        } else {
            $q = "DELETE FROM aether_user_config
                WHERE `id` = '{$this->id}'
                AND `key` = '{$key}'";
        }
        $db->query($q);
        $this->loadValues();
    }
    
    /**
     * Saves all values
     *
     * @return void
     */
    public function save() {
        if (is_array($this->values)) {
            $db = $this->sl->getDatabase("pubsys");
            foreach ($this->values as $key => $value) {
                $value = mysql_escape_string($value); 
                $q = "INSERT INTO aether_user_config 
                            (`id`, `key`, `value`)
                        VALUES
                            ($this->id, '$key', '$value')
                        ON DUPLICATE KEY UPDATE value = '$value'";
                $db->query($q);
            }
        }
    }
}
