<?php

namespace Core\Http;

/**
 * Description of Request
 *
 * @author Thomas Darko
 */
class Request {

    private static $instance = null;

    /**
     * 
     * @return string
     */
    public function getUri() {
          $full = $uri = $_SERVER['REQUEST_URI'];

        if (strpos($full, '/'.ROOT_DIR_NAME) == 0) {
            $uri = str_replace('/'.ROOT_DIR_NAME, '', $full) ;
        }

        return $uri;
    }

    /**
     * 
     * @return string
     */
    public function getDirectUri() {
        $full = $uri = $_SERVER['REDIRECT_URL'];

        if (strpos($full, '/'.ROOT_DIR_NAME) == 0) {
            $uri = str_replace('/'.ROOT_DIR_NAME, '', $full) ;
        }

        return $uri;
    }

    /**
     * 
     * @return string
     */
    public function getQueryString() {

        return $_SERVER['QUERY_STRING'];
    }

    /**
     * 
     * @return string
     */
    public function getParams() {

        $result = array_merge($_POST, $_GET);

        $request_body = file_get_contents('php://input');
        $data = json_decode($request_body, true);
        if ($data) {
            $result = array_merge($result, $data);
        }
        return $result;
    }

    /**
     * 
     * @return string
     */
    public function getParam($key) {
        $data = $this->getParams();
        return isset($data[$key]) ? $data[$key] : null;
    }

    /**
     * 
     * @return string
     */
    public function getFile($key) {
        return isset($_FILES[$key]) ? $_FILES[$key] : null;
    }

    /**
     * 
     * @return string
     */
    public function getFiles() {
        return $_FILES;
    }

    /**
     * 
     * @return string
     */
    public function getSingleFile() {
        return count($_FILES) > 0 ? $_FILES[0] : null;
    }

    /**
     * 
     * @return string
     */
    public function getPreviousUri() {

        return $_SERVER['HTTP_REFERER'];
    }

    private function __construct() {
        
    }

    /**
     * 
     * @return Request
     */
    public static function getInstance() {
        if (!self::$instance) {

            self::$instance = new Request();
        }

        return self::$instance;
    }

    /**
     * @tutorial : this function returns a value which is set in the post
     * 
     * @param string $name
     * @param any $value
     */
    public function setPost($name, $value) {

        $_POST[$name] = $value;
    }

    /**
     * 
     * @param string $name
     * @param any $value
     */
    public function setGet($name, $value) {

        $_GET[$name] = $value;
    }

    /**
     * Short description of method existPost
     *
     * @access public
     * @author Thomas Darko,  
     * @return boolean
     */
    public function existParams(Array $keys) {
        $result = true;
        $data = $this->getParams();
        foreach ($keys as $key) {
            if (!isset($data[$key])) {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * Short description of method isPost
     *
     * @access public
     * @author Thomas Darko,  
     * @return boolean
     */
    public function isPost() {
        return count($_POST) !== 0;
    }

    /**
     * Short description of method isPost
     *
     * @access public
     * @author Thomas Darko,  
     * @return boolean
     */
    public function isGet() {
        return count($_GET) !== 0;
    }

}
