<?php

namespace AdminPack\Controller;

use Core\Dto\UserAccount;

/**
 * Description of LoginCtrl
 *
 * @author Thomas Darko
 */
class LoginCtrl extends \Core\Controller {

    public function login() {

        if ($this->currentUser()->isAuthenticated()) {

            $this->redirect('adm.dash');
        }

        $this->setCoreLayout()->setTitle('Login')->renderView();
    }

    public function check() {

        $data = new UserAccount(['firstname' => 'Thomas']);

        $this->currentUser()->login($data);

        $this->redirect('adm.dash.index');
    }

    public function logout() {


        $this->currentUser()->logout();

        return $this->redirect('adm.login');
    }
    
    
    


}
