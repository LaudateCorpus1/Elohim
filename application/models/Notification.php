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
                 ->setIntegrityCheck(false)
                 ->from($this->_name)
                 ->join('Topic', 'Topic.topicId='.$this->_name.'.topicId', 'title')
                 ->where($this->getAdapter()->quoteInto('toUserId = ?', $userId))
                 ->where('beenRead = 0')
                 ->where('type IS NULL');
         //Zend_Debug::dump($query->__toString()); exit;
        return $this->fetchAll($query);
    }
    
    public function getPrivilegeNotifications($userId)
    {
        $query = $this->select()
                 ->from($this->_name, array('id', 'message', 'type'))
                 ->where($this->getAdapter()->quoteInto('toUserId = ?', $userId))
                 ->where('beenRead = 0')
                 ->where('(type = "GAINED-PRIVILEGE" OR type = "LOST-PRIVILEGE")');
        
        return $this->fetchAll($query);
    }
    
    public function addNotification(array $data)
    {
        $data['beenRead'] = false;
        $data['date'] = date('Y-m-d H:i:s', time());
        return $this->insert($data);
    }
    
    public function updateNotification(array $data, $id)
    {
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        $this->update($data, $where);
    }
    
    public function updateNotifications(array $data, array $aWhere)
    {
        $where = array();
        foreach($aWhere as $column => $value)
            $where[] = $this->getAdapter()->quoteInto($column. '= ?', $value);
 
        $where[] = 'beenRead = 0';
        $this->update($data, $where);
    }
}

