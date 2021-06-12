<?php

namespace SitePack\Controller;

/**
 * Description of HomeCtrl
 *
 * @author Thomas Darko
 */
class HomeCtrl extends \Core\Controller {

    /**
     * loads user data after login
     */
    public function index() { 

        $this->renderView();
    }
    
    /**
     * 
     *  
     */
    public function search() {
        
        $input= $this->request()->getParam('user_input');
        
         $list = $this->table('xx')->findBy($input);

         $service = $this->service('sample');
         
         $this->addData('result', $list);
         
        $this->renderResponse();
    }

}
