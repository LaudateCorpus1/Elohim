<?php

class Api_Model_Category extends Zend_Db_Table_Abstract
{
    protected $_name = 'reminder_category';
        
    public function getNames()
    {
        $query = $this->select();
        $query->from($this->_name, 'name')
              ->order($this->_name.'.name ASC');
        
        return $this->fetchAll($query);
    }
    
    public function getNamesFormFormatted()
    {
        $query = $this->select()
                      ->from($this->_name);
        
        $rows = $this->fetchAll($query);
        $names = array('' => '---------');
        foreach($rows as $row)
        {
            $names[$row->id] = $row->name;
        }
        return $names;
    }
}
