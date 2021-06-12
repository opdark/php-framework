<?php

namespace Entity;

/**
 * Description of Account
 *
 * @author Thomas Darko
 */
class Account extends \Core\Model\Entity{
    
    private $username ;
    private $password ;
    private $tatus ;
    private $userId ;
    private $studentId ;
    
    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getStudentId() {
        return $this->studentId;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function setUserId($userId) {
        $this->userId = $userId;
    }

    public function setStudentId($studentId) {
        $this->studentId = $studentId;
    }

    public function getTatus() {
        return $this->tatus;
    }

    public function setTatus($tatus) {
        $this->tatus = $tatus;
    }



}
