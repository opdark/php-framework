<?php

namespace Core\Config;

use Core\Application;
use Core\Utility\FileManager;
use Core\Config\Pack;
use Core\Dto\Account;

/**
 * Description of Config
 *
 * @author Thomas Darko
 */
class Config {

    /**
     *
     * @var Application 
     */
    private $app;
    private $data = array();
    private static $instance = null;

    private function __construct(Application $app) {
        $this->app = $app;
        $this->loadConfigFiles();
    }

    private function loadConfigFiles() {
        $this->data['config'] = FileManager::xml2array(ROOT_DIR . '/config.xml');
        $this->data['container'] = FileManager::xml2array(ROOT_DIR . '/container.xml');
    }

    /**
     * 
     * @return Config
     */
    public static function getInstance(Application $app) {
        if (!self::$instance) {
            self::$instance = new Config($app);
        }
        return self::$instance;
    }

    /**
     * 
     * @return Array
     */
    public function getParams() {
        $params = [];
        if (isset($this->data['config']['params'])) {
            $params = $this->data['config']['params'];
        }

        return $params;
    }

    /**
     * 
     * @return Array
     */
    public function getParameter($key) {
        $value = null;
        if (isset($this->getParams()[$key])) {
            $value = $this->getParams()[$key];
        }

        return $value;
    }

    /**
     * 
     * @return Pack[]
     */
    public function getPacks() {

        $val = $this->data;
        $pack_array = [];
        if (array_key_exists('pack', $val['config']['packs'])) {
            $num = count($val['config']['packs']['pack']);

            // case only one pack is defined
            if ($num == 1) {
                $attributes = $val['config']['packs']['pack']['@attributes'];
                $pack_array[0] = new Pack($attributes);
            }

            // case we have more than one pack
            elseif ($num > 1) {
                for ($i = 0; $i < count($val['config']['packs']['pack']); $i++) {
                    $attributes = $val['config']['packs']['pack'][$i]['@attributes'];
                    $pack_array[$i] = new Pack($attributes);
                }
            }
        }

        return $pack_array;
    }

    /**
     * 
     * @return Pack 
     */
    public function getPackByName($name) {

        foreach ($this->getPacks() as $pack) {
            if ($pack->getName() == $name) {
                return $pack;
            }
        }
    }

    public function getServices() {

        $val = $this->data;
        $new_array = [];
        if (array_key_exists('service', $val['container']['services'])) {
            $num = count($val['container']['services']['service']);
            if ($num == 1) {
                $new_array[0] = $val['container']['services']['service']['@attributes'];
            } elseif ($num > 1) {
                for ($i = 0; $i < count($val['container']['services']['service']); $i++) {
                    $new_array[$i] = $val['container']['services']['service'][$i]['@attributes'];
                }
            }
        }

        return $new_array;
    }

    public function getDatabases() {

        $dbs = array();


        $val = $this->data;

        if (array_key_exists('database', $val['config']['databases'])) {
            $num = count($val['config']['databases']['database']);
            if ($num == 1) {
                $dbs[] = $val['config']['databases']['database']['@attributes'];
            } elseif ($num > 1) {
                for ($i = 0; $i < count($val['config']['databases']['database']); $i++) {
                    $dbs[] = $val['config']['databases']['database'][$i]['@attributes'];
                }
            }
        }

        return $dbs;
    }

    public function getProviders() {

        $val = $this->data;
        $new_array = [];
        if (array_key_exists('provider', $val['container']['providers'])) {
            $num = count($val['container']['providers']['provider']);
            if ($num == 1) {
                $new_array[0] = $val['container']['providers']['provider']['@attributes'];
            } elseif ($num > 1) {
                for ($i = 0; $i < count($val['container']['providers']['provider']); $i++) {
                    $new_array[$i] = $val['container']['providers']['provider'][$i]['@attributes'];
                }
            }
        }

        return $new_array;
    }

    /**
     * 
     * @return Application
     */
    public function getApp() {
        return $this->app;
    }

    /**
     * 
     * @return Account
     */
    public function getSuperAdmin() {
        $admin = null;

        if (array_key_exists('superadmin', $this->data['config'])) {

            $attribs = $this->data['config']['superadmin'] ['@attributes'];
            $admin = new Account($attribs);
        }

        return $admin;
    }

    /**
     * 
     * @return []
     */
    public function getDefaultRoutes() {

        $routes = [];

        if (array_key_exists('app-routes-default', $this->data['config'])) {

            $routes = $this->data['config']['app-routes-default'];
        }

        return $routes;
    }

    /**
     * 
     * @param string $name : _login or  _404 or  _500
     * @return type
     */
    public function getDefaultRouteName($name) {

        $attributes = (array_key_exists('route-names', $this->getDefaultRoutes())) ?
                $this->getDefaultRoutes()['route-names'] ['@attributes']: [];


       return (array_key_exists(trim($name), $attributes) ) ?
                
                $attributes[trim($name)] : "" ;
    }
    
    
    
    public function getAllRoutes() {
        
        $routes = [];
        $all = (array_key_exists('route-all', $this->getDefaultRoutes())) ?
                $this->getDefaultRoutes()['route-all'] : [];

        if ($all) {

            $routes = $all ['@attributes'];
        }

        return $routes;
    }

    /**
     *  @return boolean
     */
    public function getUrlIncludeProjectFolder() {
        $result = false;

        if (array_key_exists('routes-includes-project-folder', $this->data['config'])) {

            $content = $this->data['config']['routes-includes-project-folder'] ['@attributes'];
            $result = trim($content['value']) == 'true';
        }

        return $result;
    }

}
