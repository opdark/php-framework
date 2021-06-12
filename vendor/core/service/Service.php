<?php

namespace Core\Service;

use Core\Application;

/**
 * Description of Service
 *
 * @author Thomas Darko
 */
class Service {

    /**
     *
     * @var Service[] 
     */
    protected $dependencies;
    
    protected $app;

    public function __construct(Application $app,  $dependencies ) {

        $this->app = $app;
        $this->dependencies = $dependencies ;
    }
 

    /**
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
