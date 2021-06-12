<?php

namespace Core\Model;

use Core\Model\Table;
use Core\Model\Database;    
use Core\Application;

/**
 * Short description of class Manager
 *
 * @access public
 * @author Thomas Darko,  
 */
class Manager {

    /**
     *
     * @var \Core\Application 
     */
    private $app;

    /**
     *
     * @var Database[] 
     */
    private $DAO = array();
    private $tables = array();

    /**
     *
     * @var Manager 
     */
    private static $_instance = null;

    public function __construct(Application $app) {
        $this->app = $app;
    }

    /**
     * 
     * @param type $information
     * @param type $response
     * @return type
     */
    public static function getInstance(Application $app) {


        if (!self::$_instance) {

            self::$_instance = new Manager($app);
        }

        return self::$_instance;
    }

    /**
     * Short description of method getRepossitory
     *
     * @access public
     * @author Thomas Darko,  
     * @return Table
     */
    public function getTable($tableName, $db_index = 1) {
        if (!is_string($tableName) || empty($tableName)) {
            throw new \InvalidArgumentException('The table name is invalid !');
        }
        
        

        if (!isset($this->tables[$db_index][$tableName])) {

            //building the reel name of the table and entity
            $tableClass = 'Table\\' . ucfirst($tableName) . 'Table';
            $entityClass = 'Entity\\' . ucfirst($tableName);

            // case the table class exists
            if (class_exists($tableClass)) {
                (class_exists($entityClass))? "": $entityClass=$tableName ;// we asume the name is the table
                $this->tables[$db_index][$tableName] = new $tableClass($this->getDAO($db_index), $entityClass, $this);
            }

            // case where the entity class exists but not the table class
            elseif (class_exists($entityClass)) {
                $this->tables[$db_index][$tableName] = new Table($this->getDAO($db_index), $entityClass, $this);
            }
            //case where the table name doesn't correspond to any class of entity or table
            //the $tableName is seen as the name of a table in the database
            else {
                $this->tables[$db_index][$tableName] = new Table($this->getDAO($db_index), $tableName, $this);
            }
        }

        return $this->tables[$db_index][$tableName];
    }

    /**
     * 
     * @param type $index
     * @return type
     */
    public function getDAO($index) {


        if (!isset($this->DAO[$index - 1])) {

            $db_settings = $this->app->getContainer()->getConfig()->getDatabases();

            if (count($db_settings) >= $index) {

                $dao_info = $db_settings[$index - 1];
                 
                $this->DAO[$index - 1] = $this->createDao($dao_info);
            }

           
        }

        
        return $this->DAO[$index - 1];

    }

    
    public function createDao($dao_info) {
        
        foreach (Database::DBMS as $key => $dbms) {
           
            
            if (trim(strtolower($dao_info['dbms'])) == $key) {
                
                $class = 'Core\\Model\\Database\\' . trim($dbms);
                
                if (class_exists($class)) {
                    
                    return new $class($dao_info);
                }

                
                //case there is no class for the dbms 
                $this->app->getResponse()->halt(" There is no Class in the core to connect to '" . $dao_info['dbms'] . "' databases !");
            }
        }
    }

}

?>