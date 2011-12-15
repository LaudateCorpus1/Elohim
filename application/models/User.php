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
    
    public function getByLogin($login)
    {
        $where = $this->getAdapter()->quoteInto('login = ?', $login);
        $row = $this->fetchRow($this->select()->where($where));
        if($row == null)
            throw new Exception("Utilisateur introuvable");
        return $row;
    }
    
    public function getTopicsByLogin($login)
    {
        $query = $this->select();
        $query->setIntegrityCheck(false)
              ->from($this->_name)
              ->join('Topic', 'Topic.userId = '.$this->_name.'.id',array(
                                'topicId',
                                'title',
                                'vote',
                                'date'
                                ))
              ->where($this->getAdapter()->quoteInto($this->_name.'.login = ?',$login))
              ->where('Topic.status != "closed"')
              ->order('Topic.date DESC');

        //$res = $this->fetchAll($query);
        
        return $query;
    }
    
    public function getMessagesByLogin($login)
    {
        $query = $this->select();
        $query->setIntegrityCheck(false)
              ->from($this->_name)
              ->join('Messages', 'Messages.userId = '.$this->_name.'.id',array(
                                'topicId',
                                'messageId',
                                'content',
                                'vote_message' => 'vote'
                                ))
              ->join('Topic', 'Topic.topicId = Messages.topicId',array(
                                'title',
                                'vote_topic' => 'vote'
                                ))
              ->where($this->getAdapter()->quoteInto($this->_name.'.login = ?',$login))
              ->order('Messages.date DESC');

        //$res = $this->fetchAll($query);
        
        return $query;
    }
    
    public function getAll()
    {
        return $this->fetchAll();
    }
    
    public function add(array $data)
    {
        $data['password'] = md5($data['password']);
        return $this->insert($data);
    }

    public function deleteUser($id)
    {
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        $this->delete($where);
    }

    public function updateUser(array $data, $id)
    {
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        $this->update($data, $where);
    }
    
    public function setKarma($karma, $user_id)
    {
        $new_karma = array('karma' => new Zend_Db_Expr('karma + '.$karma));
        $this->updateUser($new_karma, $user_id);
    }
    
    public function getVotes($user_id)
    {
        $query = $this->select();
        $query->setIntegrityCheck(false)
              ->from($this->_name,null)
              ->join('vote', 'vote.userId = user.id',array(
                                'messageId',
                                'type'
                                ))
              ->where($this->getAdapter()->quoteInto($this->_name.'.id = ?',$user_id));

        $res = $this->fetchAll($query)->toArray();
        
        $votes = array();
        foreach ($res as $vote)
        {
            $votes[$vote['type']][$vote['messageId']] = $vote['messageId'];
        }
        $aVotes = array('votes' => $votes);
        return $aVotes;
    }
    
    public function getFavoritesTags()
    {
        
    }
}

