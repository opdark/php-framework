<?php

namespace AdminPack\Controller;


/**
 * Description of Dashboard
 *
 * @author Thomas Darko
 */
class DashboardCtrl extends \Core\Controller {

  
   
    public function index() {
      
         $this ->redirect('adm.dash.manager');
    }
    
    public function manager() {
      
        $this->setTitle('Admin Dashboard')  ->renderView();
    }
    
    public function superAdmin() {
      
         $this->setTitle('Super Admin Dashboard') ->renderView();
    }
    
   

}
