<?php

namespace Core;

use Core\Application;
use Core\Http\Request; 
use Core\Routing\Route;


/**
 * Description of Controller
 *
 * @author Thomas Darko
 */
class Controller {

    /**
     *
     * @var Application 
     */
    protected $app;

    /**
     * 
     * @param Application $app
     */
    public function __construct(Application $app) {
        $this->app = $app;
        $this->setTitle("Home");
    }
    
    
    /**
     * 
     * @param type $value
     * 
     * @return Controller
     */
    public function addMessage($value) {
        $this->response()->addMessage($value);
        return $this;
    }
    
    /**
     * 
     * @param type $key
     * @param type $value
     * 
     * @return Controller
     */
    public function addData($key, $value) {
        $this->response()->addData($key, $value);
        return $this;
    }
    
    /**
     * 
     * @param string $title
     * 
     * @return Controller
     */
    public function setTitle($title) {
        $this->response()->setTitle($title);
        return $this;
    }
    
    /**
     * 
     * @param Array $param
     * 
     * @return Controller
     */
    public function setData($param) {
        $this->response()->setData($param);
        return $this;
    }
    
    /**
     * 
     * @param string $param
     * @return Controller
     */
    public function setLayout($param) {
        $this->response()->setTemplateName($param);
        return $this;
    }
    
    public function setCoreLayout() {
        $this->response()->setCoreTemplate();
        return $this;
    }
    
    /**
     * 
     * @param string $error
     */
    public function halt($error='Unexpected problem happened!') {
        $this->response()->halt($error);
    }
    

    /**
     * 
     * @param string $action
     * @return bool
     */
    public function hasMethod($action) {

        return(method_exists($this, $action));
    }

    /**
     * 
     * @return \Core\Service\Service
     */
    public function service($name) {
        return $this->app->getContainer()->getService($name);
    }

    /**
     * 
     * @return \Core\Model\Manager
     */
    public function manager() {
        return $this->app->getContainer()->getManager();
    }
    
    
    /**
     * 
     * @param string $tableName
     * @param int $db_index
     * @return Model\Table
     */
    public function table($tableName, $db_index=1) {
        return $this->manager()->getTable($tableName, $db_index);
    }

    /**
     * 
     * @return Request
     */
    public function request() {
        return $this->app->getContainer()->getRequest() ;
    }
    /**
     * 
     * @return Routing\Router
     */
    public function router() {
        return $this->app->getContainer()->getRouter() ;
    }

    
     /**
     * 
     * @return \Core\Service\Provider
     */
    public function provider($name) {
        return $this->app->getContainer()->getProvider($name);
    }

     /**
     * 
     * @return Route
     */
    public function currentRoute() {
        return $this->app->getCurrentRoute();
    }
     /**
     * 
     * @return Core\Dto\User
     */
    public function currentUser() {
        return $this->app->getUser();
    }
  
     /**
     * 
     * @return \Core\Config\Pack
     */
    public function currentPack() {
        return $this->app->getCurrentPack();
    }

    /**redirect: it redirects to a specific route
     * 
     * @param type string  $routeName
     * @param array $params
     */
    public function redirect($routeName,$params=[]) {
        return $this->app->redirect($routeName, $params);
    }

    
    /**
     * goBack: it redirects to the previous link
     * 
     */
    public function goBack() {
        $previous = $this->request()->getPreviousUri();
        $this->response()->redirect($previous);
    }
    
    

    
    /**
     * 
     * @param 
     * @return \Core\Http\Response
     */
    public function response() {
        
        return $this->app->getResponse();
    }
    
    /**
     * renderResponse: render a response base on the format set on pack, otherwise it renders a view
     * 
     * @return type
     */
    public function renderResponse($format="") {
        // case no format is given, we use the one defined on the pack
        if(!$format){
            $format = $this->currentPack()->getFormat();
        }
        
        // case the one defined is html or nothing is defined, we asume that it's a vue the user wants
        if(!$format || trim($format) == 'html'){
            $this->renderView() ;
        }
        
        // case a format is given, we use it to generate a vue
        else{
            $this->response()->setFormat($format);
             $this->response()->send();
        }
    }

    /**
     *  renderView: renders only an html pages
     * 
     * @param type $viewName
     * @return \Core\Http\Response
     */
    public function renderView($viewName='') {
        
        $viewFile = $this->getViewFile($viewName) ;
        
         $response = $this->response() ;
         // we are erasing the format set in the pack because we are sending a view
         $response->setFormat('html'); 
         $response->setViewPath($viewFile) ;
         $response->send();
        
    }
    
    
    public function getViewFile($viewName = '') {
        
        $file = '';

        // view in form of Pack:Controller:View
        if (strpos($viewName, ':') && (explode(':', $viewName) > 2)) {
            $tab = explode(':', $viewName);
            $file = ROOT_DIR . '/pack/' 
                    . strtolower(str_replace('Pack', '', $tab[0]))  
                    . '/view/' . strtolower($tab[1]) 
                    . '/' . $tab[2] . '.phtml';
        }
        // incase of simple name of the view
        elseif ($viewName) {
            $pack = $this->app->getCurrentPack();
            $file = ROOT_DIR . '/pack/'
                    . strtolower(str_replace('Pack', '', $pack->getName()) )
                    . '/view/' . strtolower($this->currentRoute()->getControllerName())
                    . '/' . $viewName . '.phtml';
        }
        // case function called but no view is provided
        else {
            $pack = $this->app->getCurrentPack();
            $file = ROOT_DIR . '/pack/'
                    . strtolower(str_replace('Pack', '', $pack->getName()))   
                    . '/view/' .strtolower( $this->currentRoute()->getControllerName())
                    . '/' . $this->currentRoute()->getAction() . '.phtml';
        }
        
       
        return $file;
    }
    
    
    /**
     * 
     * @return Config\Config
     */
    public function config() {
        return $this->app->getContainer()->getConfig();
    }
    
    /**
     * 
     * @param type $errorString
     * @return \Core\HTTP\Response
     */
    public function addError($errorString) {
        return  $this->currentUser()->addError($errorString);
    }
    
    /**
     * 
     * @param type $notice
     * @return \Core\HTTP\Response
     */
    public function addNotification($notice) {
        return  $this->currentUser()->addNotification($notice);
    }
}
