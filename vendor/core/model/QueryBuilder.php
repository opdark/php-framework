<?php

namespace Core\Model;

/**
 * Description of QueryBuilder
 *
 * @author Mensah
 */
class QueryBuilder {

    private $fields = [];
    private $tables = [];
    private $currentTable =' ';
    private $resultClass = '';
    private $conditions = [];
    private $query = '';
    private $dao;
    private $unique = false;
    private $limit = -1;
    
    /**
     * 
     * @param \Core\Model\Database $dao
     * @param type $table
     */
    public function __construct(\Core\Model\Database $dao, $table, $className=' ') {
        $this->dao = $dao;
        $this->currentTable = $table ;
        $this->resultClass = $className ;
    }
    
    /**
     * 
     * @param type $fields = ['field1','field2', ...]
     * @return \Core\Model\Table\QueryBuilder
     */
    public function select($fields = ['*']) {
        $this->fields = $fields;
        return $this;
    }

    /**
     * 
     * @param array $tables = ['table1','table2']
     * @return \Core\Model\Table\QueryBuilder
     */
    public function from($tables = []) {
        $this->tables = $tables;
        return $this;
    }

    /**
     * where: 
     * @example path description
     * 
     * @param Array $conds array("table.id = 2", cond2, ...)
     * @return \Core\Model\Table\QueryBuilder
     */
    public function where($conds = []) {
        foreach ($conds as $cond) {
            array_push($this->conditions, $cond);
        }
        return $this;
    }

    /**
     * 
     * @param boolean $unique
     * @return \Core\Model\Table\QueryBuilder
     */
    public function findOne( $unique = true) {
        $this->unique = $unique;
        return $this;
    }
    /**
     * 
     * @param int $max
     * @return \Core\Model\Table\QueryBuilder
     */
    public function limit( $max = -1) {
        $this->limit = intval($max);
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getSQL() {

        $this->query = ( $this->fields) ? " SELECT " . implode(",", $this->fields) : " SELECT * ";
        $this->query .= ( $this->tables) ? " FROM " . implode(",", $this->tables) : " FROM " . $this->currentTable;
        $this->query .= ( $this->conditions) ? " WHERE " . implode(" AND ", $this->conditions) : "";
        $this->query .= ( $this->limit >= 0) ? " LIMIT " . $this->limit : "";

        return $this->query;
    }

    /**
     * 
     * @return Array
     */
    public function getResult() {
        $sql = $this->getSQL();
        $this->initialize(); // to initialiaze the conditions for the nex request
        
        $result = $this->dao->query($sql,[], $this->resultClass) ;

        if ($this->unique && $result) {
            $result = $result[0] ;
        }
        
        return $result;
    }

    public function toString() {
        return $this->query;
    }

    /**
     * 
     * @return \Core\Model\Database
     */
    public function getDAO() {
        return $this->dao;
    }

    
    private function initialize() {
        $this->fields = [];
        $this->conditions = [];
        $this->limit = -1;
    }
}
