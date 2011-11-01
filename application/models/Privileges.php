<?php

class Model_Privileges extends Zend_Db_Table_Abstract
{
    protected $_name = 'privileges';
    
    public function get($type)
    {
        $id = (int)$id;
        $where = $this->getAdapter()->quoteInto('type = ?', $id);
        $row = $this->fetchRow($this->select()->where($where));
        if($row == null)
                throw new Exception("Privilege introuvable");
        return $row;
    }
    
    public function getAll()
    {
        return $this->fetchAll();
    }
}

