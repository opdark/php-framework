<?php

namespace Table;

/**
 * Description of DepartmentTable
 *
 * @author user
 */
class DepartmentTable extends \Core\Model\Table {

    public function getByGoupId($userGroupId ) {
        $attrib[':idGroup'] = $userGroupId;
        $sql = " SELECT  d.id AS id, d.name AS name, d.code AS code
                  FROM department d JOIN `user_group` ug ON ug.id_dept = d.id WHERE ug.id = :idGroup";
        return $this->query($sql, $attrib, true);
    }

    public function getRightsByGroupId($userGroupId ) {
        // control of the case where user is in super Admin group 
        // ( zero will be passed as id of group to this function)
        if($userGroupId == 0){
            return $this->query('SELECT cod AS code  FROM rights') ;
        }
        
        
        $attrib[':idGroup'] = $userGroupId;
        $sql = " SELECT r.cod AS code  FROM rights r WHERE r.id IN (
                        SELECT id_right 
                        FROM `user_group_right` ugr
                        WHERE ugr.id_usr_group = :idGroup )";
        return $this->query($sql, $attrib);
    }
    
}
