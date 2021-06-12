<?php

namespace Core\Routing;

use Core\Config\Config;

/**
 * Description of Router
 *
 * @author Thomas Darko
 */
class Router {

    private static $instance = null;
    private $config;
    private $urlIncludesProjectFolder = true ;

    /**
     * 
     * @param Config $config
     */
    private function __construct(Config $config) {

        $this->config = $config;
    }

    /**
     * 
     * @param Config $config
     * @return Router
     */
    public static function getInstance(Config $config) {

        if (!self::$instance) {
            self::$instance = new Router($config);
        }

        return self::$instance;
    }

    /**
     * 
     * @return Route[]
     */
    public function getRoutes() {
        $routes = [];
        foreach ($this->config->getPacks() as $pack) {
            $routes = array_merge($routes, $pack->getRoutes());
        }
        return $routes;
    }

    /**
     * 
     * @param string $uri
     * @return Route
     */
    public function getMatched($uri) {
                
        //looping on the packs
        foreach ($this->config->getPacks() as $pack) {
            
            //var_dump($pack->getRoutes());exit;
            
            // searching the matching route
            foreach ($pack->getRoutes() as $route) {
                
                // in case the route matches the uri
                $result = $route->matchesUri($uri);
                

                if ($result instanceof Route) {
                    return $result;
                }
            }
        }
    }
    

    /**
     * Short description of method getUriByRouteName
     *
     * @access public
     * @author Thomas Darko,  
     * @return mixed
     */
    public function getUriByRouteName($routeName, $params = array()) {
        
        $included = $this->config->getUrlIncludeProjectFolder();
       
        foreach ($this->getRoutes() as $route) {
            if ($route->matchesName($routeName)) {

                try {
                    return ($included)? '/'.ROOT_DIR_NAME. $route->getUri($params): $route->getUri($params);
                } catch (\Exception $ex) {
                    throw $ex;
                }
            }
        }

        return null;
    }

}
