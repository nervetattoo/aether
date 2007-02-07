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
     * @var AetherServiceLocator
     */
    private $sl;
    
    /**
     * Constructor. Takes a ServiceLocator object.
     *
     * @access public
     * @param AetherServiceLocator $sl
     * @param int $userId
     */
    public function __construct(AetherServiceLocator $sl, $userId) {
        $this->sl = $sl;
        $this->user = new User($userId);
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
     * Checks if a user is logged in
     *
     * @access public
     * @return boolean
     */
    public function isLoggedIn() {
    if (isset($this->user))
        return true;
    else if (isset($_SESSION['loggedInUserId']))
        return true;
    else
        return false;
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
     * Lets the user act as another.
     *
     * @access public
     * @return boolean Was the user allowed to su and does the user exist?
     */
     /*
    public function su($user = false) {
        if ($this->canSu()) {
            if (!$this->originalUserId) {
                $this->originalUserId = $this->user->userId;
                $_SESSION['originalUserId'] = $this->user->userId;
                $_SESSION['originalUsername'] = $this->user->username;
            }
            if (is_numeric($user))
                $newUser = new User($user);
            else if ($user == false)
                $newUser = new User($this->originalUserId);
            else {
                $db = $this->sl->getDatabase("neo");
                $id = $db->queryValue("SELECT brukerid FROM Bruker
                    WHERE brukernavn = '$user'");
                $newUser = new User($id);
            }
            if (is_numeric($newUser->userId)) {
                $this->user = $newUser;
                $this->config =
                    new NeoDefaultConfig($this->sl, 'user', $newUser->userId);
                $_SESSION['loggedInUserId'] = $newUser->userId;
                return true;
            }
        }
        return false;
    }
    */

    /**
     * Checks if the user is allowed to even try to su()
     *
     * @access public
     * @return boolean Is suing allowed?
     */
    /*
    public function canSu() {
        if (!isset($this->user))
            return false;
        return ($this->user->isNeoRoot || $this->originalUserId);
    }
    */

}
?>
