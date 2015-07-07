<?php

/**
 * Description of Tag
 *
 * @author jeremie
 */
class Api_Model_Sahaba extends Zend_Db_Table_Abstract
{
    protected $_name = 'sahaba';

    public function getSahaba($id)
    {
            $id = (int)$id;
            $row = $this->fetchRow(array('id = ?' => $id));
            if (!$row)
            {
                throw new Exception("Tag introuvable $id");
            }
            return $row->toArray();
    }
    
    public function getWithLimit($limit = 10, $order = 'sahaba.name DESC')
    {
        $query = $this->select();
        $query->from($this->_name, array(
                  'id',
                  'name'
                  ))
              ->order($order)
              ->limit($limit);
        
        return $this->fetchAll($query);
    }
    
    public function addSahaba(array $data)
    {
        $sahaba = array(
            'name' => $data['name']
        );
        return $this->insert($sahaba);
    }

    public function deleteSahaba($id)
    {
        $this->delete(array('id = ?' => $id));
    }

    public function updateSahaba(array $data, $id)
    {
        $this->update($data, array('id = ?' => $id));
    }
}
?>
