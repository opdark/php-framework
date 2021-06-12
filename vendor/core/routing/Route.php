<?php

namespace Core\Routing;

use Core\Error\ERR_CLI;

/**
 * Description of Routing
 *
 * @author Thomas Darko
 */
class Route {

    use \Core\Utility\Hydration;

    /**
     *
     * @var string 
     */
    private $name;

    /**
     *
     * @var string 
     */
    private $uri;

    /**
     *
     * @var string 
     */
    private $controller;

    /**
     *
     * @var string 
     */
    private $action;

    /**
     *
     * @var bool 
     */
    private $secured;

    /**
     *
     * @var Array 
     */
    private $params = array();

    /**
     *
     * @var string[] 
     */
    private $middlewares = array();

    public function __construct(Array $params) {

        $this->hydarate($params);
    }

    /**
     * 
     * @return string
     */
    public function getName() {

        return $this->name;
    }

    /**
     * 
     * @return string
     */
    public function getPackName() {
        $names = explode('\\', $this->controller);
        $packname = $names[0];

        return $packname;
    }

    /**
     * 
     * @return Array
     */
    public function getparams() {

        return $this->params;
    }

    /**
     * 
     * @return string
     */
    public function getAction() {

        return $this->action;
    }

    /**
     * 
     * @return string[]
     */
    public function getMiddlewares() {

        return $this->middlewares;
    }

    /**
     * 
     * @param \Core\Application $app
     * @return \Core\Controller
     */
    public function getController($app) {

        $controller = null;

        if (class_exists($this->controller)) {

            $controller = new $this->controller($app);
        }

        return $controller;
    }

    public function getControllerName() {
        $tab = explode('\\', $this->controller);
        return str_replace('Ctrl', '', end($tab));
    }

    public function setController($controller) {

        $this->controller = $controller;
    }

    public function setUri($uri) {

        $this->uri = $uri;
    }

    public function setMiddlewares($param) {

        $names = explode(',', $param);
        foreach ($names as $name) {
            $middlewareName = trim($name);
            if ($middlewareName) {
                $this->middlewares[] = 'Middleware\\' . $middlewareName;
            }
        }
    }

    public function getParamsValue() {
        $result = [];
        foreach ($this->params as $name => $details) {
            $result[$name] = $details['value'];
        }

        return $result;
    }

    public function getParamValue($key) {
        foreach ($this->params as $name => $details) {
            if (trim($name) == trim($key)) {
                return $details['value'];
            }
        }
    }

    /**
     * Short description of method match
     *
     * @access public
     * @author Thomas Darko,  
     * @return Route
     */
    public function matchesUri($uri) {
        
        
        $result = null;
        if (preg_match('`^' . $this->uri . '$`', $uri, $matches)) {
            $result = $this;
        }

        return $result;
    }

    /**
     * Short description of method match
     *
     * @access public
     * @author Thomas Darko,  
     * @return boolean
     */
    public function matchesQueryString($uriQueryString) {
        $result = false;

        if (!$this->hasParams()) {
            $result = true;
        }
        // case params exists
        else {
            $uriParams = $this->getUriParams($uriQueryString);

            //if all the required params are exactly defined in the uri
            $requiredParamsSent = array_intersect_key($uriParams, $this->params);
            if ((count($this->params) == count($requiredParamsSent))) {
                $this->setParamValues($uriParams);

                // if all the params values match their patterns
                if ($this->isParamsValid()) {
                    $result = true;
                }
            }
        }
        return $result;
    }

    public function hasParams() {
        return count($this->params);
    }

    /**
     * 
     * @param type $params
     * @return type
     * @throws \Exception
     */
    public function getUri($params = array()) {
        $uri = $this->uri;

        if ($this->hasParams()) {
            foreach ($params as $name => $details) {
                $uri = str_replace('{' . $name . '}', $details, $uri);
            }
        }

        // retrieving existing params that were not provided
        $extraParams = array_diff_key($this->params, $params);
        if ($extraParams) {
            $names = '';
            foreach ($extraParams as $name => $details) {
                $names .= $name . ', ';
            }

            // throw an error about the missing parameter the Parameter 

            throw new \Exception("The Parameter '" . $names . "' does not exist !");
        }

        return $uri;
    }

    /**
     * Short description of method matchesName
     *
     * @access public
     * @author Thomas Darko,  
     * @return boolean
     */
    public function matchesName($name) {
        return (strtolower($this->name) === strtolower(trim($name)));
    }

    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Short description of method setAction
     *
     * @access public
     * @author Thomas Darko,  
     * @return mixed
     */
    public function setAction($action) {
        $this->action = $action;
    }

    /**
     * Short description of method setController
     *
     * @access public
     * @author Thomas Darko,  
     * @return mixed
     */
    public function setControllerClass($controllerClass) {
        $this->controllerClass = $controllerClass;
    }

    public function setParams($stringParams = '') {

        $defaultPattern = '(.+)';
        $params = array();

        if ($stringParams) {

            $listParams = explode(',', $stringParams);

            foreach ($listParams as $oneParam) {

                $details = explode('|', $oneParam);

                $name = trim($details[0]);
                $extra['pattern'] = (isset($details[1])) ? trim($details[1]) : $defaultPattern;
                $extra['value'] = (isset($details[2])) ? trim($details[2]) : null;

                $params[$name] = $extra;
            }

            $this->params = $params;
        }
    }

    public function setParamValues($list = array()) {
        foreach ($list as $name => $value) {
            if (isset($this->params[$name])) {
                $this->params[$name]['value'] = $value;
            }
        }
    }

    /**
     * 
     * @param type $secured
     */
    public function setSecured($secured = true) {
        $this->secured = boolval($secured);
    }

    public function toArray() {
        $result['uri'] = $this->uri;

        foreach ($this->params as $key => $value) {
            $result['params'][$key] = $value['pattern'];
        }

        return $result;
    }

    /**
     * 
     * @param type $paramsUri
     * @return type
     */
    public function getUriParams($paramsUri) {
        $result = [];
        $params = explode('&', $paramsUri);

        foreach ($params as $keyValue) {
            $string = trim($keyValue);
            if (($string != '') && (strpos($string, '='))) {
                $detail = explode('=', $string);
                $result[$detail[0]] = $detail[1];
            }
        }
        return $result;
    }

    /**
     * 
     * @return boolean
     */
    public function isParamsValid() {
        $result = TRUE;
        foreach ($this->params as $key) {
            if (!preg_match('`^' . $key['pattern'] . '$`', $key['value'])) {
                return FALSE;
            }
        }

        return $result;
    }

    /**
     * 
     * @return boolean
     */
    public function isSecured() {
        return $this->secured;
    }

}
