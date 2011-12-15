<?php

class Model_Notification extends Zend_Db_Table_Abstract
{
    protected $_name = 'notification';
    
    public function get($type)
    {
        $where = $this->getAdapter()->quoteInto('type = ?', $type);
        $row = $this->fetchRow($this->select()->where($where));
        if($row == null)
                throw new Exception("Privilege introuvable");
        return $row;
    }
    
    public function getAllByUser($userId)
    {
        $where = $this->getAdapter()->quoteInto('toUserId = ?', $userId);
        return $this->fetchAll($where);
    }
    
    public function getAllUnreadByUser($userId)
    {
        $query = $this->select()
                 ->where($this->getAdapter()->quoteInto('toUserId = ?', $userId))
                 ->where('beenRead = 0');
        return $this->fetchAll($query);
    }
    
    public function addNotification(array $data)
    {
        $data['read'] = false;
        return $this->insert($data);
    }
}

