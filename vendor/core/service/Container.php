<?php

namespace Core\Service;

use Core\Config\Config;
use Core\Routing\Router;
use Core\Http\Request;
use Core\Model\Manager;
use Core\Application;

/**
 * Description of Container
 *
 * @author Thomas Darko
 */
class Container {

    CONST SERVICE_KEY = 'serv';
    CONST PROVIDER_KEY = 'pro';

    /**
     *
     * @var Container 
     */
    private static $instance = null;

    /**
     *
     * @var array[service,provider] 
     */
    private $wrapper;

    /**
     *
     * @var \Core\Application 
     */
    private $app;

    /**
     * 
     * @param Application $app
     */
    private function __construct(Application $app) {
        $this->app = $app;
        $this->loadDefaultServices();
    }

    /**
     * 
     * @return Container $this
     */
    public function loadDefaultServices() {
        $config = Config::getInstance($this->app);
        $this->wrapper[self::SERVICE_KEY]['request'] = Request::getInstance();
        $this->wrapper[self::SERVICE_KEY]['config'] = $config;
        $this->wrapper[self::SERVICE_KEY]['router'] = Router::getInstance($config);
        $this->wrapper[self::SERVICE_KEY]['router'] = Router::getInstance($config);
        $this->wrapper[self::PROVIDER_KEY] = [];
        return $this;
    }

    /**
     * 
     * @param Application $app
     * @return Container 
     */
    public static function getInstance(Application $app) {

        if (!self::$instance) {
            self::$instance = new Container($app);
        }

        return self::$instance;
    }

    public function addService($name, $object) {
        $this->wrapper[self::SERVICE_KEY][$name] = $object;
    }

    public function addProvider($name, $object) {
        $this->wrapper[self::PROVIDER_KEY][$name] = $object;
    }

    public function addContent($field, $name, $object) {
        $this->wrapper[$field][$name] = $object;
    }

    /**
     * 
     * @param string $name
     * @return Provider
     */
    public function getProvider($name) {
        return $this->getContent(self::PROVIDER_KEY, $name);
    }

    /**
     * 
     * @param type $name
     * @return Service
     */
    public function getService($name) {
        return $this->getContent(self::SERVICE_KEY, $name);
    }

    /**
     * @return Manager
     */
    public function getManager() {

        if (!$this->exists(self::SERVICE_KEY, 'manager')) {
            $this->wrapper[self::SERVICE_KEY]['manager'] = Manager::getInstance($this->app);
        }

        return $this->getService('manager');
    }

    /**
     * 
     * @return Router
     */
    public function getRouter() {
        return $this->wrapper[self::SERVICE_KEY]['router'];
    }

    /**
     * 
     * @return Request
     */
    public function getRequest() {
        return $this->wrapper[self::SERVICE_KEY]['request'];
    }

    /**
     * 
     * @return Config
     */
    public function getConfig() {
        return $this->wrapper[self::SERVICE_KEY]['config'];
    }

    public function exists($field, $nameService) {
        return isset($this->wrapper[$field][$nameService]);
    }

    /**
     * getService : 
     * 
     * @param string $name
     * @return \Core\Application\serviceClass
     * @throws \RuntimeException
     */
    public function getContent($field, $name) {

        $content = null;

        // if the object doesn't exist in the container then we create it
        if ($this->exists($field, $name)) {
            $content = $this->wrapper[$field][$name];
        }
        // case we are retreivin the entity manager
        elseif (trim($name) == 'manager') {
            $content = $this->getManager() ;
        }
        // if the object doesn't exist in the container then we create it
        else {

            // loading the service or provider from the config
            $info = [];
            switch ($field) {
                case self::PROVIDER_KEY : $info = $this->getConfig()->getProviders();
                    break;
                default: $info = $this->getConfig()->getServices();
                    break;
            }



            foreach ($info as $serviceProvider) {




                // if the service is defined, then we create an instance of it
                if ($serviceProvider['name'] == $name) {



                    // getting the class of the service
                    $serviceClass = $serviceProvider['class'];

                    // initializing the parameter to be used in the instanciation
                    $injections = array();

                    // preparing the injected services into the $params
                    if (isset($serviceProvider['injections']) && trim($serviceProvider['injections'])) {


                        $injectedServicesName = explode(',', trim($serviceProvider['injections']));

                        foreach ($injectedServicesName as $name2) {
                            $injections[$name2] = $this->getContent($field, $name2);
                        }
                    }



                    if (!class_exists($serviceClass)) {
                        $this->app->getResponse()->halt("The class '" . $serviceClass . "' is not accessible by the container!");
                    }

                    // ceating the instance of the new service or Entitymanager 
                    $content = new $serviceClass($this->app, $injections);

                    //adding the object to the container
                    $this->addContent($field, $name, $content);
                }
            }
        }


        return $content;
    }

    /**
     * 
     * @return Application
     */
    public function getApp() {
        return $this->app;
    }

}
