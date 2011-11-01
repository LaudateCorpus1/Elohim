<?php

class Model_User extends Zend_Db_Table_Abstract
{
    protected $_name = 'user';
    
    public function get($id)
    {
        $id = (int)$id;
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        $row = $this->fetchRow($this->select()->where($where));
        if($row == null)
                throw new Exception("Utilisateur introuvable");
        return $row;
    }
    
    public function getAll()
    {
        return $this->fetchAll();
    }
    
    public function add(array $data)
    {
            return $this->insert($data);
    }

    public function delete($id)
    {
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        $this->delete($where);
    }

    public function update(array $data, $id)
    {
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        $this->update($data, $where);
    }
}

