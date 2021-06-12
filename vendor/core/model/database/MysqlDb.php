<?php

namespace Core\Model\Database;

use Core\Model\Database as DB;
use PDO;

/**
 * Short description of class PDO
 *
 * @access public
 * @author Thomas Darko,  
 */
class MysqlDb extends DB {

    use \Core\Utility\Hydration;

    private $pdo;

    public function __construct(Array $params) {

        $this->hydarate($params);

        $this->getConnexion();
    }


    /**
     * 
     */
    private function getConnexion() {

        if ($this->pdo === NULL) {
            $link = 'mysql:dbname=' . $this->name . '; host=' . $this->host;
            $pdo = new \PDO($link, $this->user, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo = $pdo;
        }
    }

    
    /**
     * 
     * @return \PDO
     */
    public function pdo() {
        return $this->pdo;
    }
    
    
    /**
     * Short description of method isSelectQuery
     *
     * @access public
     * @author Thomas Darko,  
     * @return mixed
     */
    public function isSelectQuery($statement = '') {

        $sql = strtolower(trim($statement));

        return strpos($sql, 'select') === 0;
    }

    /**
     * query 
     * 
     * @param type $statement
     * @param type $attrib
     * @param type $class_name
     * @param type $one
     * @return array
     */
    public function query($statement, $attrib = [], $class_name = null, $one = false) {
        $data = [];
        if ($attrib) {
            $pdoStatement = $this->pdo->prepare($statement);
            $data = $pdoStatement->execute($attrib);
        } else {
            $pdoStatement = $this->pdo->query($statement);
        }


        if ($this->isSelectQuery($statement)) {

            if ($class_name == null) {
                $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
            } else {
                $pdoStatement->setFetchMode(PDO::FETCH_CLASS, $class_name);
            }

            if ($one) {
                $data = $pdoStatement->fetch();
            } else {
                $data = $pdoStatement->fetchAll();
            }
        }
        $pdoStatement = null ;

        return $data;
    }

  
}

?>