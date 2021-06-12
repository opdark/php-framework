<?php

namespace Core\Http;

use Core\Application;
use Core\Page\Page;
use Core\Utility\Format;

/**
 * Description of Response
 *
 * @author Thomas Darko
 */
class Response {

    const TEMPLATE_DIR = ROOT_DIR . '/web/template/';

    /**
     * Short description of attribute $title
     *
     * @access private
     * @var string
     */
    private $title;

    /**
     * Short description of attribute $format
     *
     * @access private
     * @var string
     */
    private $format;

    /**
     * Short description of attribute $viewPath
     *
     * @access private
     * @var string
     */
    private $viewPath;

    /**
     * Short description of attribute $templateName
     *
     * @access private
     * @var string
     */
    private $templateName;

    /**
     * contains the status code of the executed action
     *
     * @access private
     * @var int
     */
    private $_status;

    /**
     * contains the error made by the developper
     *
     * @access private
     * @var string
     */
    private $_internalError;

    /**
     * contains the various errors and notifications to send to the clients
     *
     * @access private
     * @var Array
     */
    private $_messages = [];

    /**
     *  contains the data to send to the user
     *
     * @access private
     * @var string
     */
    private $_data = [];

    /**
     *
     * @var Application 
     */
    public $app;

    /**
     * 
     * @param Application $app
     * @param type $format
     */
    public function __construct(Application $app, $format = 'html') {
        $this->app = $app;
        $this->_status = 200;
        //header('Access-Control-Allow-Origin: *');
        $this->setFormat($format);
    }

    public function send() {
        // preparation of response header based on the format
        $this->prepareHeaders();

        //case format is XHTML, we send a proper view
        if ($this->format == "html") {
            $this->sendHTML();
        }
        // sending JSON or XML
        else {
            exit($this->getResult());
        }
    }

    /**
     * 
     */
    private function sendHTML() {

        
      

        // creation of new page
        $page = new Page($this->app);

        // verification if the viewFile is not a file then we display the error view
        if ($this->_internalError) {
            exit($page->getGeneratedPage($this->getCoreTemplate(), $this->getErrorViewPath(), $this->getResult(), $this->title));
        }

        // verification  if the view layout is not valid,
        if (!file_exists($this->viewPath)) {
            $this->title = 'Internal Error';
            $this->_internalError = " The view '" . $this->viewPath . "' specified does not exist !";
            exit($page->getGeneratedPage($this->getCoreTemplate(), $this->getErrorViewPath(), $this->getResult(), $this->title));
        }

        
        // verification  if the current layout is core,
        if ($this->templateName == $this->getCoreTemplate()) {

            exit($page->getGeneratedPage($this->getCoreTemplate(),  $this->viewPath, $this->getResult(), $this->title));
        }
        
        // verification  if the current layout is not valid,
        if ($this->templateName && !$this->isValidTemplate()) {

            $this->title = 'Internal Error';
            $this->_internalError = " The template '" . $this->templateName . "' specified in the controller method, does not exist in the template folder!";
            exit($page->getGeneratedPage($this->getCoreTemplate(), $this->getErrorViewPath(), $this->getResult(), $this->title));
        }
        
        //case the action is given a valid template then we use it

        if($this->templateName && $this->isValidTemplate()){
             exit($page->getGeneratedPage($this->getTemplate(), $this->viewPath, $this->getResult(), $this->title));
        }

        // case above conditions are not respected and bundle has template
        $this->templateName = $this->app->getCurrentPack()->getTemplate();
        if($this->templateName && !$this->isValidTemplate()){
             exit($page->getGeneratedPage($this->getTemplate(), $this->viewPath, $this->getResult(), $this->title));
        }

        //case the templates depends on a user
        if (!$this->templateName && $this->app->getUser()->isAuthenticated()) {
            // if no template is set in the action but one is defined in the role of the current user,  then we take it
            $this->templateName = $this->app->getUser()->getTemplate();

            // if no valid template is set in user's role, but one is defined in the pack of the route,  then we take layout of pack defined in config.xml
            if (!$this->isValidTemplate() && $this->app->getCurrentRoute()) {
                $this->templateName = $this->app->getCurrentPack()->getTemplate();
            }
        }

        //the teplate name is valid, then we use it to generate the page
        if ($this->isValidTemplate()) {
            exit($page->getGeneratedPage($this->getTemplate(), $this->viewPath, $this->getResult(), $this->title));
        }
        //still if the templateName is not valid, then we generate the html page with the core template 
        else {
            // case has no layout  then we take the default layout of the application
            exit($page->getGeneratedPage($this->getCoreTemplate(), $this->viewPath, $this->getResult(), $this->title));
        }
    }

    /**
     * 
     * @param array $value
     */
    public function setData(Array $value) {
        $this->_data = $value;
        return $this;
    }

    /**
     * 
     * @param Response 
     */
    public function setViewPath($viewPath) {
        $this->viewPath = $viewPath;

        return $this;
    }

    /**
     * 
     * @param Response 
     */
    public function setTemplateName($templateName) {
        $this->templateName = $templateName;
        return $this;
    }
    public function setCoreTemplate() {
        $this->templateName = $this->getCoreTemplate();
        return $this;
    }

    /**
     * 
     * @param string $error
     */
    public function halt($error = 'Unexpected problem happened!') {
        $this->title = "Internal Error";
        $this->_status = 500;
        $this->_internalError = $error;
        //no template is needed if the format is not html
        ($this->format != 'html') ? '' : $this->viewPath = $this->getErrorViewPath();
        $this->send();
    }

    /**
     * 
     * @param int $value
     */
    public function addData($key, $value) {
        $this->_data[$key] = $value;
        return $this;
    }

    /**
     * 
     * @param type $header
     */
    public function addHeader($header) {
        header($header);
        return $this;
    }

    /**
     * 
     * @param array $value
     */
    public function addMessage($value) {
        $this->_messages[] = $value;
        return $this;
    }

    /**
     * 
     * @param type $format
     */
    public function setFormat($format) {
        $this->format = trim(strtolower($format));
        return $this;
    }

    /**
     * 
     * @param type $title
     */
    public function setTitle($title) {
        $this->title = trim(($title));
        return $this;
    }

    /**
     * 
     * @param type $location
     */
    public function redirect($location) {
        header('Location: ' . $location);
        exit;
    }

    /**
     * 
     */
    private function prepareHeaders() {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type");
        if ($this->format == "xml" || $this->format == "wsdl") {
            header("Content-Type: application/xml");
        } elseif ($this->format == "json") {
            header("Content-Type: application/json");
        }
    }

    /**
     * 
     */
    private function getResult() {
        $result['_status'] = $this->_status;
        $result['_message'] = $this->_messages;
        $result['_internalError'] = $this->_internalError;
        $result['_data'] = $this->_data;

        // if format is xml the we send an xml result
        if ($this->format == "xml") {
            $array = json_decode(json_encode($result), true);
            return Format::array2xml($array);

            // else if format =json then we send a json result
        } else if ($this->format == "json") {
            return json_encode($result);
        }

        // else we send an array result
        else if ($this->format == "html") {
            return $result;
        }
    }

    public function getInternalError() {
        return $this->_internalError;
    }

    public function getFormat() {
        return trim(strtolower($this->format));
    }

    public function getErrorViewPath() {
        return ROOT_DIR . '/vendor/core/template/error.phtml';
    }

    public function getCoreTemplate() {
        return ROOT_DIR . '/vendor/core/template/default.phtml';
    }

    /**
     * 
     * @param Response 
     */
    public function getTemplate() {
        return self::TEMPLATE_DIR . $this->templateName . '.phtml';
    }

    public function isValidTemplate() {
        return file_exists($this->getTemplate());
    }

}
