<?php

class Model_News extends Zend_Db_Table_Abstract
{
    protected $_name = 'news';
    
    public function get($id)
    {
        $id = (int)$id;
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        $row = $this->fetchRow($this->select()->where($where));
        if($row == null)
        {
                throw new Exception("News introuvable : $id");
        }
        return $row;
    }
    
    public function getAll()
    {
        return $this->fetchAll();
    }
}

