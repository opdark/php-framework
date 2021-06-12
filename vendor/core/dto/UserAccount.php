<?php

namespace Core\Dto;

if (!session_status() === PHP_SESSION_ACTIVE) {
    session_start();
}

use Shared\Constant\UserCode;

/**
 * Short description of class UserAccount
 *
 * @access public
 * @author Thomas Darko,  
 */
class UserAccount extends Dto {

    private $id;
    private $firstname;
    private $lastname;
    private $username;
    private $password;
    private $status;
    private $roleId;
    private $code;
    private $description;

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

    

    public function getId() {
        return $this->id;
    }

    public function getFirstname() {
        return $this->firstname;
    }

    public function getLastname() {
        return $this->lastname;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getRoleId() {
        return $this->roleId;
    }

    public function getCode() {
        return $this->code;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setFirstname($firstname) {
        $this->firstname = $firstname;
    }

    public function setLastname($lastname) {
        $this->lastname = $lastname;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function setRoleId($roleId) {
        $this->roleId = $roleId;
    }

    public function setCode($code) {
        $this->code = $code;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function isPasswordValid($password) {
        $result = false;
        if ($this->code == UserCode::SUP_ADMIN) {
            $result = ($password == $this->password);
        }
        else {
            $result = (md5($password) == $this->password);
        }
        
        return $result;
    }

    public function getRoleName() {
        $role = $this->getRole();
        return $role ? $role['name'] : 'UNDEFINED';
    }

    public function isStudent() {
        $role = $this->getRole();
        return $role ? $role['code'] == UserCode::STUDENT : false;
    }

    public function isLecturer() {
        $role = $this->getRole();
        return $role ? $role['code'] == UserCode::LECTURER : false;
    }

    public function isAdmin() {
        $role = $this->getRole();
        return $role ? $role['code'] == UserCode::ADMIN : false;
    }

    public function isSuperAdmin() {
        $role = $this->getRole();
        return $role ? $role['code'] == UserCode::SUP_ADMIN : false;
    }

}

?>