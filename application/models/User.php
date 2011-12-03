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
    
    public function getAll()
    {
        return $this->fetchAll();
    }
    
    public function add(array $data)
    {
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
}

