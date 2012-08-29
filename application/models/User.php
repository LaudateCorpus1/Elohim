<?php

class Model_User extends Zend_Db_Table_Abstract
{
    protected $_name = 'user';
    
    public function get($id, $tags = false)
    {
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        $row = $this->fetchRow($this->select()->where($where));
        unset($row->password);
        
        if($row == null)
            throw new Exception("Utilisateur introuvable : $id");
        
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
                  //'t.amount_topics',
                  //'m.amount_messages',
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
              /*->joinLeft(array('t' => new Zend_Db_Expr('(SELECT t1.userId, COUNT(t1.topicId) AS amount_topics
                                                         FROM Topic t1
                                                         GROUP BY t1.userId)')
                              ), 't.userId = u.id', null)
              ->joinLeft(array('m' => new Zend_Db_Expr('(SELECT m1.userId, COUNT(m1.messageId) AS amount_messages
                                                         FROM Messages m1
                                                         GROUP BY m1.userId)')
                              ), 'm.userId = u.id', null)*/
              ->where($this->getAdapter()->quoteInto('u.login = ?', $login));
        
        //Zend_Debug::dump($query->__toString()); exit;
        $row = $this->fetchRow($query);
        if($row == null)
            throw new Exception("Utilisateur introuvable : $login");
        
        return $row;
    }
    
    public function getById($id)
    {
        $query = $this->select();
        $query->setIntegrityCheck(false)
              ->from(array('u' => $this->_name), array(
                  //'t.amount_topics',
                  //'m.amount_messages',
                  'u.date_created',
                  'u.login',
                  'u.karma',
                  'email',
                  'last_name',
                  'first_name',
                  'location',
                  'u.last_connexion',
                  'u.id',
                  'u.avatar'
                  ))
              /*->joinLeft(array('t' => new Zend_Db_Expr('(SELECT t1.userId, COUNT(t1.topicId) AS amount_topics
                                                         FROM Topic t1
                                                         GROUP BY t1.userId)')
                              ), 't.userId = u.id', null)
              ->joinLeft(array('m' => new Zend_Db_Expr('(SELECT m1.userId, COUNT(m1.messageId) AS amount_messages
                                                         FROM Messages m1
                                                         GROUP BY m1.userId)')
                              ), 'm.userId = u.id', null)*/
              ->where($this->getAdapter()->quoteInto('u.id = ?', $id));
        
        $row = $this->fetchRow($query);
        if($row == null)
            throw new Exception("Utilisateur introuvable : $id");
        
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
        return $this->select()->from($this->_name)->order('date_created DESC');
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
        if(isset($data['password']))
            $data['password'] = md5($data['password']);
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        $this->update($data, $where);
    }
    
    public function updateUserByEmail(array $data, $email)
    {
        if(isset($data['password']))
            $data['password'] = md5($data['password']);
        $where = $this->getAdapter()->quoteInto('email = ?', $email);
        $this->update($data, $where);
    }
    
    public function setKarma($karma, $user_id)
    {
        $model_privileges = new Model_Privileges();
        $karma_privileges = $model_privileges->getDistinct();
        
        // Il faut vérifier si l'utilisateur gagne ou perd un privilège
        /*$currentKarma = $this->get($user_id)->karma;
        foreach($karma_privileges as $privilege)
        {
            // Il gagne un privilège
            if((intval($currentKarma) < $privilege->karma_needed)  && ((intval($currentKarma) + intval($karma)) >= $privilege->karma_needed))
            {
                Zend_Controller_Action_HelperBroker::getStaticHelper('NotifyUser')
                        ->direct($privilege->gained_message, $user_id, null, null, 'GAINED-PRIVILEGE');
            }
            // Il perd un privilège
            else if((intval($currentKarma) > $privilege->karma_needed)  && ((intval($currentKarma) + intval($karma)) < $privilege->karma_needed))
            {
                Zend_Controller_Action_HelperBroker::getStaticHelper('NotifyUser')
                        ->direct($privilege->lost_message, $user_id, null, null, 'LOST-PRIVILEGE');
            }
        }*/
        
        $new_karma = array('karma' => new Zend_Db_Expr('karma + '.$karma));
        
        $this->updateUser($new_karma, $user_id);
    }
    
    public function getVotes($user_id)
    {
        $query = $this->select();
        $query->setIntegrityCheck(false)
              ->from($this->_name, null)
              ->join('vote', 'vote.userId = user.id', array(
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
    
    public function getFavoriteDocuments($userId)
    {
        $query = $this->select();
        $query->setIntegrityCheck(false)
              ->from('favoriteLibrary', null)
              ->join('library', 'favoriteLibrary.libraryId = library.id',array(
                                'title',
                                'vote',
                                'id'
                                ))
              ->join('user', 'library.userId = user.id',array(
                                'authorId' => 'id',
                                'login'
                                ))
              ->where($this->getAdapter()->quoteInto('favoriteLibrary.userId = ?', $userId))
              ->order('id DESC');
        
        return $query;
    }
    
    public function getKarmaStats($userId)
    {
        $query = $this->select();
        $query->setIntegrityCheck(false)
              ->distinct()
              ->from('karma', array(
                                'type',
                                'fromUserId',
                                'libraryId',
                                'topicId',
                                'messageId',
                                'date'
                                ))
              ->joinLeft('library', 'karma.libraryId = library.id', array(
                                'documentTitle' => 'title'
                                ))
              ->joinLeft('Topic', 'karma.topicId = Topic.topicId', array(
                                'topicTitle' => 'title'
                                ))
              ->joinLeft('Messages', 'karma.messageId = Messages.messageId', array(
                                'content'
                                ))
              ->joinLeft($this->_name, $this->_name.'.id = library.userId', array(
                                'login'
                                ))
              ->where($this->getAdapter()->quoteInto('karma.toUserId = ?', $userId))
              ->where('karma.outdated = 0')
              ->where('karma.cancellation = 0')
              ->order('karma.date DESC')
//              ->group('DAY(karma.date)')
//              ->group('karma.topicId')
//              ->group('karma.messageId')
//              ->group('karma.libraryId')
              ->limit(50);
        
        return $this->fetchAll($query);
    }
    
    public function doesEmailExist($email)
    {
        $query = $this->select();
        $query->from($this->_name)
              ->where($this->getAdapter()->quoteInto('email = ?', $email));
        $res = $this->fetchRow($query);
        if($res == null)
            return false;
        return true;
    }
    
    public function getUsersAmount()
    {
        $query = $this->select()
                      ->from($this->_name, 'count(*) as amount');
        $res = $this->fetchRow($query);
        return $res->amount;
    }
}

