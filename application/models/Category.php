<?php

class Model_Category extends Zend_Db_Table_Abstract
{
    protected $_name = 'category';
    
    public function get($id)
    {
        $id = (int)$id;
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        $row = $this->fetchRow($this->select()->where($where));
        if($row == null)
        {
                throw new Exception("Categorie introuvable: $id");
        }
        return $row;
    }
    
    public function getAll()
    {
        return $this->fetchAll();
    }
    
    public function getNames()
    {
        $query = $this->select()
                      ->from($this->_name, 'name');
        
        $rows = $this->fetchAll($query);
        $names = array();
        foreach($rows as $row)
        {
            $names[] = $row->name;
        }
        return $names;
    }
    
    public function getNamesFormFormatted()
    {
        $query = $this->select()
                      ->from($this->_name, 'name');
        
        $rows = $this->fetchAll($query);
        $names = array('' => 'Choisissez');
        foreach($rows as $index => $row)
        {
            $names[$index+1] = $row->name;
        }
        return $names;
    }

}
