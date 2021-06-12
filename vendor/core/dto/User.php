<?php

namespace Core\Dto;

if (!session_start()) {
    session_start();
}

use Shared\Constant\UserCode;

/**
 * Short description of class User
 *
 * @access public
 * @author Thomas Darko,  
 */
class User extends Dto {

    const SESSION_KEY = 'KEY';
    const ERROR_KEY = 'KO';
    const SUCCESS_KEY = 'OK';

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Thomas Darko,  
     * @return mixed
     */
    public function __construct(array $attribs = []) {
        parent::__construct($attribs);
    }

    /**
     * Short description of method isAuthenticated
     *
     * @access public
     * @author Thomas Darko,  
     * @return mixed
     */
    public function isAuthenticated() {
        return isset($_SESSION[self::SESSION_KEY]);
    }

    /**
     * Short description of method login
     *
     * @access public
     * @author Thomas Darko,  
     * @return null
     */
    public function login(UserAccount $data) {
        $_SESSION[self::SESSION_KEY] = $data;
    }

    /**
     * Short description of method logout
     *
     * @access public
     * @author Thomas Darko,  
     * @return mixed
     */
    public function logout() {
        unset($_SESSION[self::SESSION_KEY]);
        unset($_SESSION[self::ERROR_KEY]);
        unset($_SESSION[self::SUCCESS_KEY]);
        session_destroy() ;
    }

    /**
     * getAccount
     * 
     * @return UserAccount
     */
    public function getAccount() {
        $result = null;
        if ($this->isAuthenticated()) {
            $result = $_SESSION[self::SESSION_KEY];
        }
        return $result;
    }

    public function getRoleName() {
        return $this->getAccount()->getRoleName();
    }
    public function getRoleCode() {
        return $this->getAccount()->getCode();
    }


    public function isStudent() {
        return $this->getRoleCode() == UserCode::STUDENT ;
    }

    public function isLecturer() {
        return $this->getRoleCode() == UserCode::LECTURER;
    }

    public function isAdmin() {
        return $this->getRoleCode() == UserCode::ADMIN ;
    }

    public function isSuperAdmin() {
        return $this->getRoleCode() == UserCode::SUP_ADMIN ;
    }
//
//    public function get($key = 'id') {
//        return $this->getSession($key);
//    }

    /**
     * 
     * @param type $key
     * @return UserAccount
     */
    public function getSession($key = '') {

        $result = false;

        if ($this->isAuthenticated()) {
            if (!$key) {
                $result = $_SESSION[self::SESSION_KEY];
            } elseif (isset($_SESSION[self::SESSION_KEY][$key])) {
                $result = $_SESSION[self::SESSION_KEY][$key];
            }
        }


        return $result;
    }

    public function set($key, $value) {
        return $this->setSession($key, $value);
    }

    /**
     * 
     * @param type $key
     * @param type $value
     */
    public function setSession($key, $value) {
        if ($this->isAuthenticated()) {
            $_SESSION[self::SESSION_KEY][$key] = $value;
        }
    }

    public function addError($err) {
        $_SESSION[self::ERROR_KEY][] = $err;
    }

    public function addNotification($notif) {
        $_SESSION[self::SUCCESS_KEY][] = $notif;
    }

    public function hasErrors() {
        return isset($_SESSION[self::ERROR_KEY]);
    }

    public function hasNotifications() {
        return isset($_SESSION[self::SUCCESS_KEY]);
    }

    public function getErrors() {
        return $this->getFlash(self::ERROR_KEY);
    }

    public function getTemplate() {
        return $this->getSession('template');
    }
    public function getNotifications() {
        return $this->getFlash(self::SUCCESS_KEY);
    }

    public function getFlash($key) {
        $result = [];
        if (isset($_SESSION[$key])) {
            $result = $_SESSION[$key];
            unset($_SESSION[$key]);
        }
        return $result;
    }

}

?>