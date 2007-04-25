<?php
/*
HARDWARE.NO EDITORSETTINGS:
vim:set tabstop=4:
vim:set shiftwidth=4:
vim:set smarttab:
vim:set expandtab:
*/

require_once('/home/lib/libDefines.lib.php');
require_once(LIB_PATH . '/user/User.lib.php');
require_once(AETHER_PATH . 'lib/AetherUserConfig.php');

/**
 * AetherUser object.
 * More or less a facade over User and some sort of to-come session storage
 * 
 * Created: 2007-02-07
 * @author Raymond Julin
 * @package aether.lib
 */

class AetherUser {
    /**
     * Holds a standard user object.
     * This class does not extend the User class, simply because
     * the user object is not fit as we hope to implement features like su
     * @var User 
     */
    public $user;

    /**
     * Holds the ServiceLocator
     * @var ServiceLocator
     */
    private $sl;

    /**
     * Holds the AetherUserConfig object for this user
     * @var AetherUserConfig
     */
    private $config;
    
    /**
     * Constructor. Takes a ServiceLocator object.
     *
     * @access public
     * @param ServiceLocator $sl
     * @param int $userId
     */
    public function __construct(ServiceLocator $sl, $userId) {
        $this->sl = $sl;
        $this->user = new User($userId);
        $this->config  = new AetherUserConfig($sl, $userId);
    }
    
    /**
     * Fetch a single describing property of this user
     *
     * @access public
     * @return mixed
     * @param $key
     */
    public function get($key) {
        return $this->user->$key;
    }

    /**
     * Checks privileges
     *
     * @return boolean does the user have this privilege?
     * @param string/array $privilege 
     * @param string $site What site to check against
     */
    public function hasPrivilege($privilege = 'any', $site = 'any') {
        if (!isset($this->user))
            return false;
        // SUpport for checking if user has one of privileges
        if (is_array($privilege)) {
            foreach ($privilege as $pri) {
                if ($this->user->hasPrivilege($pri, $site))
                    return true;
            }
            return false;
        }
        return $this->user->hasPrivilege($privilege, $site);
    }

    /**
     * Returns user config
     *
     * @access public
     * @return AetherUserConfig
     */
    public function getConfig() {
        return $this->config;
    }
}
?>
