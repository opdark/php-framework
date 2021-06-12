<?php

namespace Core\Service;

use Core\Application ;
/**
 * Description of Provider
 *
 * @author Thomas Darko
 */
class Provider {
    
    private $instance;
    
    private $app;

    private function __construct(Application $app) {
        $this->app = $app ;
       
    }
    
    public static function getInstance($param) {
        
        if(!self::$instance){
            self::$instance = new Provider($param);
        }
        return self::$instance;
    }
    
    
    /**
     * getDepedency : 
     * 
     * @param type $serviceName
     * @return Service
     */
    protected function getDepedency($serviceName) {

        if (!isset($this->dependencies[$serviceName])) {
            $this->app->getResponse()->halt("The Service '" . $serviceName . "' is not a dependency to " . get_called_class());
        }

        return $this->dependencies[$serviceName];
    }

}
