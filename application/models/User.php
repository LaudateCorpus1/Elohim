<?php

class Model_User extends Zend_Db_Table_Abstract
{
    protected $_name = 'user';
    
    public function get($id, $tags = false)
    {
        //if(!$tags)
        {
            $where = $this->getAdapter()->quoteInto('id = ?', $id);
            $row = $this->fetchRow($this->select()->where($where));
            unset($row->password);
        }
        /*else
        {
            $query = $this->select()
                      ->setIntegrityCheck(false)
                      ->from($this->_name)
                      ->join('FavoritesTags', 'FavoritesTags.userId = '.$this->_name.'.id', null)
                      ->join('Tags', 'Tags.tagId = FavoritesTags.tagId', array('name', 'tagId'))
                      ->where($this->getAdapter()->quoteInto($this->_name.'.id = ?', $id));
            
            $row = $this->fetchAll($query);
        }*/
        
        if($row == null)
            throw new Exception("Utilisateur introuvable");
        
        if($tags)
        {
            $query = $this->select()
                      ->setIntegrityCheck(false)
                      ->from('Tags',array(
                                        'tagId',
                                        'name'
                                        ))
                      ->join('FavoritesTags', 'Tags.tagId = FavoritesTags.tagId', null)
                      ->where('FavoritesTags.userId = ?',$id);

            $fav_tags = $this->fetchAll($query)->toArray();
            $row = (object)array_merge($row->toArray(), array('favtags' => $fav_tags));
        }
        
        return $row;
    }
    
    public function getByLogin($login)
    {
        $query = $this->select();
        $query->setIntegrityCheck(false)
              ->from(array('u' => $this->_name), array(
                  't.amount_topics',
                  'm.amount_messages',
                  'u.date_created',
                  'u.login',
                  'u.karma',
                  'email',
                  'last_name',
                  'first_name',
                  'location',
                  'u.last_connexion',
                  'u.id',
                  'u.password',
                  'u.avatar'
                  ))
              ->joinLeft(array('t' => new Zend_Db_Expr('(SELECT t1.userId, COUNT(t1.topicId) AS amount_topics
                                                         FROM Topic t1
                                                         GROUP BY t1.userId)')
                              ), 't.userId = u.id', null)
              ->joinLeft(array('m' => new Zend_Db_Expr('(SELECT m1.userId, COUNT(m1.messageId) AS amount_messages
                                                         FROM Messages m1
                                                         GROUP BY m1.userId)')
                              ), 'm.userId = u.id', null)
              ->where($this->getAdapter()->quoteInto('u.login = ?', $login));
        
        //Zend_Debug::dump($query->__toString()); exit;
        $row = $this->fetchRow($query);
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

