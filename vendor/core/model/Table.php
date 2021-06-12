<?php

namespace Core\Model;

use Core\Model\Entity;

/**
 * Short description of class Table
 *
 * @access public
 * @author Thomas Darko,  
 */
class Table {

    /**
     * Short description of attribute dao
     *
     * @access public
     * @var Database
     */
    protected $dao = null;
    protected $name = '';
    protected $tableAlias = 't';
    protected $entityClass = '';

    /**
     *
     * @var Manager 
     */
    protected $manager;

    /**
     *
     * @var QueryBuilder 
     */
    protected $queryBuilder;

    /**
     * 
     * @param \Core\Model\Database $dao
     * @param string $entityClass : Model\Entity\Name or 
     */
    public function __construct(\Core\Model\Database $dao, $entityClass, $em) {


        //getting the name of the table from the current object
        $name = explode('\\', get_class($this));
        $this->name = strtolower(str_replace('Table', '', end($name)));


        if (class_exists($entityClass)) {
            $this->entityClass = $entityClass;
        }

        //case the entity class does not exist then $entityClass is used as the name of the table
        else {
            $this->name = strtolower($entityClass);
        }

        $this->dao = $dao;
        $this->queryBuilder = new QueryBuilder($dao, $this->name, $this->entityClass);
        $this->manager = $em;
    }

    /**
     * Short description of method query
     * $statement, 
     * $attrib = [], 
     * $one = false
     *
     * @access public
     * @author Thomas Darko,  
     * @return mixed
     */
    public function query($statement, $attrib = [], $one = false) {
        return $this->queryBuilder->getDAO()->query($statement, $attrib, $this->entityClass, $one);
    }

    public function execute($statement, $attrib = []) {
        return $this->queryBuilder->getDAO()->query($statement, $attrib, $this->entityClass, $one);
    }

    /**
     * 
     * @param Entity or array $entity
     * @return type
     */
    public function add($entity) {
        if ($entity) {
            $names = $val = $placeholder = null;
            $entityArray = (is_array($entity)) ? $entity : $entity->toArray();

            foreach ($entityArray as $key => $value) {
                $names .= ',' . $key;
                $placeholder .= ',?';
                $val[] = trim(stripslashes($value));
            }

            $statement = 'INSERT INTO ' . $this->name
                    . '( ' . trim($names, ',') . ')'
                    . ' VALUE (' . trim($placeholder, ',') . ') ; ';

            $success = $this->query($statement, $val);

            return ($success) ? $this->last() : null;
        }
    }

    /**
     * 
     * @return int
     */
    public function lastId() {
        $result = $this->query('SELECT MAX(id) AS id FROM ' . $this->name, [], true);
        return (int) $result['id'];
    }

    /**
     * 
     * @return type
     */
    public function last() {
        $sql = 'SELECT * FROM ' . $this->name . '  WHERE Id IN ( SELECT MAX(id) FROM ' . $this->name . '  ) ';
        return $this->query($sql, [], true);
    }

    /**
     * 
     * @param Entity or array $entity
     * @return Entity
     */
    public function update($entity, $referenceFields = ['id'], $getUpdatedRows = true) {

        if ($entity) {

            $result = $nbField = null;
            $conditions = $attribs = $vals = null;

            $entityArray = (is_array($entity)) ? $entity : $entity->toArray();
            // preparing the UPDATE statement
            foreach ($entityArray as $key => $value) {

                //we are not updating the reference keys in the DB! because they will be used in the conditions clause

                if (!in_array(trim($key), $referenceFields)) {
                    $attribs .= $this->tableAlias . '.' . $key . '= :' . $key . " , ";
                    $vals[':' . $key] = trim($value);
                }
            }




            if ($referenceFields) {

                // preparing the WHERE statement
                foreach ($referenceFields as $field) {
                    $conditions .= " " . $this->tableAlias . "." . $field . "= '" . $entity[$field] . "' AND";
                }
                
                $sql = " UPDATE " . $this->name . " " . $this->tableAlias . " SET " . trim(trim($attribs), ",") . " WHERE " . trim(trim($conditions, "AND")) . " ";
                $result = $this->query($sql, $vals);
                if ($result && $getUpdatedRows) {
                    foreach ($referenceFields as $f) {
                        $param[$f] = trim($entity[$f]);
                    }
                    $result = $this->findOneBy($param);
                }
            }
        }


        return $result;
    }

    public function updateOrAdd($entity, $referenceFields = ['ID']) {
        $result = $this->update($entity, $referenceFields);


        return ($result) ? $result : $this->add($entity);
    }

    /**
     * 
     * @param int $id
     * @return Entity
     */
    public function find(int $id) {
        return $this->query(" SELECT * from {$this->name} WHERE id = ? ", [$id], true);
    }

    /**
     * 
     * @return \ArrayObject
     */
    public function all() {

        $statement = ' SELECT * FROM ' . $this->name . ' ORDER BY id DESC';

        return $this->query($statement);
    }

    /**
     * 
     * @param type $param = ['field1' => 'val1', 'field2' => 'val2']
     * @return type
     */
    public function findBy($param) {
        $conditions = [];
        foreach ($param as $key => $value) {
            $conditions[] = $key . " = '" . $value . "' ";
        }

        //queryBuilder is a fluent object
        return $this->queryBuilder->where($conditions)->getResult();
    }

    /**
     * 
     * @param type $param = ['field1' => 'val1', 'field2' => 'val2']
     * @return type
     */
    public function findOneBy($param) {
        $conditions = [];
        foreach ($param as $key => $value) {
            $conditions[] = $key . " = '" . $value . "' ";
        }

        //queryBuilder is a fluent object
        return $this->queryBuilder->where($conditions)->findOne()->getResult();
    }

    /**
     * 
     * @param type $tableName
     * @return Table
     */
    public function table($tableName) {
        return $this->manager->getTable($tableName);
    }

    public function getPDO() {
        return $this->dao->pdo();
    }

}

?>