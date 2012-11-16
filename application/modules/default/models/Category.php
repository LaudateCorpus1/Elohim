<?php

class Default_Model_Category extends Zend_Db_Table_Abstract
{
    protected $_name = 'category';
    
    public function get($id)
    {
        $id = (int)$id;
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        $row = $this->fetchRow($this->select()->where($where));
        if($row == null)
        {
                throw new Exception("Category introuvable : $id");
        }
        return $row;
    }
}

