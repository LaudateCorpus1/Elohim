<?php

class Model_Privileges extends Zend_Db_Table_Abstract
{
    protected $_name = 'privileges';
    
    public function get($type)
    {
        $where = $this->getAdapter()->quoteInto('type = ?', $type);
        $row = $this->fetchRow($this->select()->where($where));
        if($row == null)
                throw new Exception("Privilege introuvable");
        return $row;
    }
    
    public function getAll()
    {
        return $this->fetchAll();
    }
    
    public function getMRP($module, $resource, $privilege)
    {
        $row = $this->fetchRow($this->select()
                ->where('module = ?', $module)
                ->where('resource = ?', $resource)
                ->where('privilege = ?', $privilege));
       // if($row == null)
                //throw new Exception("Privilege introuvable");
        return $row;
    }
}

