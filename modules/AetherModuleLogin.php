<?php
/*
HARDWARE.NO EDITORSETTINGS:
vim:set tabstop=4:
vim:set shiftwidth=4:
vim:set smarttab:
vim:set expandtab:
*/

require_once('/home/lib/libDefines.lib.php');
require_once(AETHER_PATH . 'lib/AetherModule.php');
require_once(AETHER_PATH . 'lib/AetherActionResponse.php');
require_once(LIB_PATH . 'SessionHandler.lib.php');
require_once(LIB_PATH . 'user/UserFinder.lib.php');
require_once(LIB_PATH . 'user/User.lib.php');
require_once(LIB_PATH . 'user/UserAuthenticator.lib.php');

/**
 * 
 * User login
 * 
 * Created: 2007-02-07
 * @author Raymond Julin
 * @package aether.module
 */

class AetherModuleLogin extends AetherModule {
    
    /**
     * Render module
     *
     * @access public
     * @return string
     */
    public function render() {
        $config = $this->sl->fetchCustomObject('aetherConfig');
        // Log user in if requested
        if (!empty($_POST['username']) AND !empty($_POST['password'])) {
            // Find username (validate if user exists)
            $username = $_POST['username'];
            $password = $_POST['password'];
            $uf = new UserFinder;
            $userId = $uf->findByUsername($username);
            if (is_numeric($userId) AND $userId != 0) {
                // User exists, now validate password
                $user = new User($userId);
                $authenticator = new UserAuthenticator;
                if ($authenticator->authenticate($user, $password)) {
                    // Set user to appear as logged in (session data)
                    $session = $this->sl->fetchCustomObject('session');
                    $session->set('loggedIn', 1);
                    $session->set('userId', $userId);
                    $session->set('username', $user->username);

                    /* This is hackish from a to z. First of all
                     * we shouldnt just replace /login as other names
                     * could be used. Secondly its wrong to draw() in
                     * the actual module, this should happen in the
                     * section
                     */

                    if ($session->get('wasGoingTo')) 
                        $location = $session->get('wasGoingTo');
                    else 
                        $location = $config->getRoot();

                    $response = new AetherActionResponse(302, $location);
                    $response->draw();
                }
                else {
                    // Wrong password
                    $error = 'password';
                }
            }
            else {
                $error = 'username';
            }
        }
        $tpl = $this->sl->getTemplate(108);
        $tpl->selectTemplate('login');
        $tpl->setVar('session', $_SESSION);
        if (isset($error)) {
            $tpl->setVar('failedAt', $error);
            $tpl->setVar('username', $username);
        }
        $tpl->setVar('urlbase', $config->getRoot());
        return $tpl->returnPage();
    }
}
?>
