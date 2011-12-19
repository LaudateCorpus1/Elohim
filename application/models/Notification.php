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
                 ->join('topic', 'topic.topicId='.$this->_name.'.topicId', 'title')
                 ->where($this->getAdapter()->quoteInto('toUserId = ?', $userId))
                 ->where('beenRead = 0');
         //Zend_Debug::dump($query->__toString()); exit;
        return $this->fetchAll($query);
    }
    
    public function addNotification(array $data)
    {
        $data['beenRead'] = false;
        return $this->insert($data);
    }
    
    public function updateNotification(array $data, $id)
    {
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        $this->update($data, $where);
    }
    
    public function updateNotifications(array $data, $topicId)
    {
        $where = array();
        $where[] = $this->getAdapter()->quoteInto('topicId = ?', $topicId);
        $where[] = 'beenRead = 0';
        $this->update($data, $where);
    }
}

