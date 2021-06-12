<?php

namespace Core\Config;

use Core\Utility\FileManager;
use Core\Routing\Route;

/**
 * Description of Pack
 *
 * @author Thomas Darko
 */
class Pack {

    use \Core\Utility\Hydration;

    private $name;
    private $prefix;
    private $secured;
    private $middlewares;
    private $format;
    private $template;

    function __construct(Array $value) {
        $this->hydarate($value);
    }

    function getPrefix() {
        return $this->prefix;
    }

    function getSecured() {
        return $this->secured;
    }
    function getFormat() {
        return $this->format? strtolower($this->format): null;
    }

    function getTemplate() {
        return $this->template;
    }
    function getMiddlewares() {
        return $this->middlewares;
    }

    function setPrefix($prefix) {
        $this->prefix = $prefix;
    }

    function setSecured($secured) {
        $this->secured = $secured;
    }
    function setFormat($format) {
        $this->format = strtolower($format);
    }
    
    function setTemplate($template) {
        $this->template = $template;
    }

    public function getName() {

        return $this->name;
    }

    public function setName($param) {

        $this->name = $param;
    }
    public function setMiddlewares($param) {

        $this->middlewares = trim(trim($param,','));
    }

    /**
     * getRoutes :
     * 
     * @return Route[]
     */
    public function getRoutes() {

        $path = ROOT_DIR . '/pack/' . strtolower(str_replace('Pack', '', $this->name))   . '/routing.xml';

        $rawData = FileManager::xml2array($path);
        $array_list = [];

        // case the tag controller is defined
        if (array_key_exists('controller', $rawData)) {

            $length = count($rawData['controller']);
            $isUniqueCtrl = array_key_exists('@attributes', $rawData['controller']);

            // case at least one controller is defined with at least one route
            if ($length > 1) {
                $array_list = $this->prepare($rawData['controller'], $isUniqueCtrl);
            }
        }

        
        return $array_list;
    }

    /**
     * 
     * @param array $controllerRoutesArray
     * @param boolean $isUniqueController
     * @return Route[]
     */
    private function prepare($controllerRoutesArray, $isUniqueController) {
        $array_list = [];

        //case of only one controller in the routing file
        if ($isUniqueController) {
            $controller = $controllerRoutesArray['@attributes'];

            //retrieving the routes of unique controller case it has defined routes
            if (array_key_exists('route', $controllerRoutesArray)) {

                // we are getting the number of route in the particular controller
                $length = count($controllerRoutesArray['route']);

                // case only one route with one controller
                if ($length == 1) {
                    $r = $controllerRoutesArray['route'];
                    $route = $this->array2Route($controller, $r);
                    array_push($array_list, $route); 
                }

                // case of many routes with only one controller
                elseif ($length > 1) {
                    foreach ($controllerRoutesArray['route'] as $r) {
                        $route = $this->array2Route($controller, $r);
                        array_push($array_list, $route); 
                    }
                }
            }
        }

        // case of more than one controller in the routing file
        else {
            foreach ($controllerRoutesArray as $controllerRoutes) {

                $controller = $controllerRoutes['@attributes'];

                //retrieving the routes of unique controller case it has defined routes
                if (array_key_exists('route', $controllerRoutes)) {

                    // we are getting the number of route in the particular controller
                    $length = count($controllerRoutes['route']);

                    // case only one route with one controller
                    if ($length == 1) {

                        $r = $controllerRoutes['route'];
                        $route = $this->array2Route($controller, $r);
                        array_push($array_list, $route); 
                    }

                    // case of many routes with only one controller
                    elseif ($length > 1) {
                        foreach ($controllerRoutes['route'] as $r) {
                            $route = $this->array2Route($controller, $r);
                            array_push($array_list, $route);
                        }
                    }
                }
            }
        }

        return $array_list;
    }

    /**
     * 
     * @return Route
     */
    private function array2Route($controller, $r) {

        //by default, all the routes are secure and take the value define on the pack
        $value['secured'] = trim($this->secured)  !== "false" ;
        $value['middlewares'] = trim($this->middlewares) ;
        $value['controller'] = $this->name . '\\Controller\\' . $controller['name'] . 'Ctrl';
        
        // processing of url
        $value['uri'] =  ($this->prefix != "" && trim($this->prefix) != "/") ?  $this->prefix : "" ;
        $value['uri'] .=  ( $controller['prefix'] != "" && trim( $controller['prefix']) != "/" )? $controller['prefix'] : "" ;
        
        if(isset($r['@attributes']['path'])){
            $path = $r['@attributes']['path'];
            $value['uri'] .= ( $path !="" && $path != "/")? trim($path) : "";
            $value['uri']= ($value['uri'])? $value['uri'] : "/";
        }
        
        
        
        // update of the value of secure with the new value on the action
        isset($r['@attributes']['name']) ? $value['name'] = $r['@attributes']['name'] : "";
        isset($r['@attributes']['secured']) ? $value['secured'] =  trim($r['@attributes']['secured'])  !== "false" : "";
        isset($r['@attributes']['action']) ? $value['action'] = $r['@attributes']['action'] : "";
        isset($r['@attributes']['params']) ? $value['params'] = $r['@attributes']['params'] : "";
        isset($r['@attributes']['middlewares']) ? $value['middlewares'] = trim( $value['middlewares'].','.$r['@attributes']['middlewares'], ',') : "";

        return new Route($value);
    }

}
