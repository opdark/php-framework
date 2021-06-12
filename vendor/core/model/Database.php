<?php

namespace Core\Model; 

/**
 * Short description of class Database
 *
 * @access public
 * @author Thomas Darko,  
 */
abstract class Database
{
   
    /**
     * Short description of attribute name
     *
     * @access protected
     * @var String
     */
    protected $name = null;

    /**
     * Short description of attribute user
     *
     * @access protected
     * @var String
     */
    protected $user = null;

    /**
     * Short description of attribute password
     *
     * @access protected
     * @var String
     */
    protected $password = null;

    /**
     * Short description of attribute host
     *
     * @access protected
     * @var String
     */
    protected $host = null;
    
    const DBMS = array(
        "mysql" => "MysqlDb",
    );






    /**
     * Short description of method query
     *
     * @abstract
     * @access public
     * @author Thomas Darko,  
     * @return mixed
     */
    public abstract function query($statement, $attrib = [], $class_name = null, $one = false);

    /**
     * Short description of method query
     *
     * @abstract
     * @access public
     * @author Thomas Darko,  
     * @return \PDO
     */
    public abstract function pdo();

    
    function getName(): String {
        return $this->name;
    }

    function getUser(): String {
        return $this->user;
    }

    function getPassword(): String {
        return $this->password;
    }

    function getHost(): String {
        return $this->host;
    }


    function setName(String $name) {
        $this->name = $name;
    }

    function setUser(String $user) {
        $this->user = $user;
    }

    function setPassword(String $password) {
        $this->password = $password;
    }

    function setHost(String $host) {
        $this->host = $host;
    }

} 

?>