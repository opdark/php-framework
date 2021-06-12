<?php
namespace Middleware ;
/**
 * Description of Wsdl
 *
 * @author Thomasino
 */
class Wsdl extends \Core\Middleware {
    
    public function run() {
       $routes = $this->app->getContainer()->getRouter()->getRoutes(); 
       
       $list = [] ;
       
        foreach ($routes as $route) {
          $list[] = $route->toArray();     
        } 
        
        $this->app->getResponse()
                ->setData($list)
                ->setFormat('xml')
                ->addMessage(' ----------THE WSDL OF THE WEB SERVICE----------------- ')
                ->send();
    }
    

}
