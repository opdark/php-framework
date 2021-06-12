<?php

namespace Core\Page;

class Page {

    /**
     *
     * @var \Core\Application 
     */
    private $app;
    public $title;
    
    
    

    public function __construct(\Core\Application $app) {
        $this->app = $app;
    }

    /**
     * 
     * @param file $templateFile
     * @param file $viewFile
     * @param Array $variables
     * @return type
     */
    public function getGeneratedPage($templateFile, $viewFile, $variables, $title) {
        $this->title =$title ;
        
        extract($variables);

        ob_start();

        require $viewFile;

        $content = ob_get_clean();

        ob_start();

        
        require $templateFile;

        return ob_get_clean();
    }

    /**
     * path : generate the correct url from the routes name
     * 
     * @param type $routeName
     * @param type $params
     * @return type
     */
    public function path($routeName, $params = array()) {

        $router = $this->app->getContainer()->getRouter();

        return $router->getUriByRouteName($routeName, $params);
    }

    /**
     * setTemplate : called on the view to specify its parent layout
     * 
     * @param type $templateName
     */
    public function setTemplate($templateName) {

        $this->app->getResponse()->setTemplateName($templateName);
    }

    


    public function access($link) {
        $included = $this->app->getContainer()->getConfig()->getUrlIncludeProjectFolder();
        
        return ($included)?  ('/'.ROOT_DIR_NAME.'/web/' . $link) : ('/web/' . $link);
    }

    public function accessLocal($link) {
        return './../Pack/' . $this->app->getCurrentPack()->getName() . '/View/' . $link;
    }

    public function hasErrors() {
        return $this->app->getUser()->hasErrors();
    }
    
    public function getErrors() {
        return $this->app->getUser()->getErrors();
    }
    
    public function getInternalError() {
        return $this->app->getResponse()->getInternalError();
    }
    
    public function hasNotifications() {
        return $this->app->getUser()->hasNotifications();
    }
    
    /**
     * 
     * @return \Core\Dto\UserAccount
     */
    public function current() {
       $this->$this->app->getUser()->getAccount() ;
    }

}

?>