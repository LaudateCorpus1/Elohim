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
    
    public function addSahaba($name)
    {
        $sahaba = array(
            'name' => $name
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
    
    public function doesExist($sahaba)
    {
        $query = $this->select();
        $query->from($this->_name)
              ->where('name = ?', $sahaba);
        $res = $this->fetchRow($query);
        if($res == null)
        {
            return false;
        }
        else
        {
            return $res->id;
        }
    }
    
    public function search($term)
    {
        $filter = function($sahaba) use ($term)
        {
            $sahabas = explode(" ", $term);
            $finalterm = end($sahabas);
            if(stristr($sahaba, $finalterm))
                return true;
            return false;
        };
        $t = array();
        $sahabalist = $this->fetchAll();
        foreach($sahabalist as $sahaba)
        {
            $t[] = $sahaba->name;
        }
        return array_filter($t, $filter);
    }
}