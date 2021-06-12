<?php

namespace Core\Dto;


/**
 * Description of Account
 *
 * @author Thomasino
 */
class Account extends \Core\Dto\Dto {
    
    private $username ;
    private $password ;
    private $status ;
    private $role ;
    private $userId ;
    private $studentId ;
    
    
  
    function getUsername() {
        return $this->username;
    }

    function getPassword() {
        return $this->password;
    }

    function getStatus() {
        return $this->status;
    }

    function getRole() {
        return $this->role;
    }

    function getUserId() {
        return $this->userId;
    }

    function getStudentId() {
        return $this->studentId;
    }

    function setUsername($username) {
        $this->username = $username;
    }

    function setPassword($password) {
        $this->password = $password;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setRole($role) {
        $this->role = $role;
    }

    function setUserId($userId) {
        $this->userId = $userId;
    }

    function setStudentId($studentId) {
        $this->studentId = $studentId;
    }

        
    /**
     * 
     * @param type $password
     * @return bool
     */
    public function isPasswordValid($password) {
         return $this->password == md5($password);
    }
    


}
