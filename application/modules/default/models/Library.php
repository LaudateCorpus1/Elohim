<?php

class Default_Model_Library extends Zend_Db_Table_Abstract
{
    protected $_name = 'library';
    
    public function get($id)
    {
        $where = $this->getAdapter()->quoteInto($this->_name.'.id = ?', $id);
        $query = $this->select();
        $query->setIntegrityCheck(false)
              ->from($this->_name)
              ->join('user', $this->_name.'.userId = user.id', 'login')
              ->where($where);
        $row = $this->fetchRow($query);
        if($row == null)
        {
            throw new Exception("Document introuvable : $id");
        }
        return $row;
    }
    
    public function getAll($order = 'library.date DESC')
    {
        $query = $this->select();
        $query->setIntegrityCheck(false)
              ->from($this->_name, array(
                  'id',
                  'date',
                  'lastEditDate',
                  'title',
                  'content',
                  'public',
                  'flag',
                  'vote',
                  'userId'
                  ))
              ->join('user', $this->_name.'.userId = user.id', 'login')
              ->where('public = 1')
              ->order($order)
              ->order($this->_name.'.date DESC');
        
        return $query;
    }
    
    public function getUserId($id)
    {
        $query = $this->select()
              ->from($this->_name, 'userId')
              ->where($this->getAdapter()->quoteInto('id = ?', $id));
                
        $row = $this->fetchRow($query);
        if($row == null)
        {
            throw new Exception("Document introuvable : $id");
        }
        return $row->userId;
    }
    
    public function addDocument($userId, $title, $content, $public, $source)
    {
        $data = array(
            'userId' => $userId,
            'title' => $title,
            'content' => $content,
            'public' => $public,
            'source' => $source
        );
        return $this->insert($data);
    }
    
    public function updateDocument(array $data, $id)
    {
        $this->update($data, $this->getAdapter()->quoteInto('id = ?', $id));
    }
    
    public function deleteDocument($id)
    {
        $this->delete(array('id = ?' => $id));
    }
    
    public function getByUsername($username, $flagOwner = false)
    {
        $modelUser = new Model_User();
        if($modelUser->doesExist($username))
        {
            $query = $this->select();
            $query->setIntegrityCheck(false)
                  ->from($this->_name, array(
                      'id',
                      'date',
                      'lastEditDate',
                      'title',
                      'content',
                      'public',
                      'flag',
                      'vote'
                      ))
                  ->join('user', $this->_name.'.userId = user.id', null)
                  ->where($this->getAdapter()->quoteInto('login = ?', $username))
                  ->order('date DESC');

            if(!$flagOwner)
                $query->where('public = 1');

            return $query;
        }
        else
            throw new Exception("Cet utilisateur n'existe pas");
    }
    
    public function getTags($libraryId)
    {
        $query = $this->select();
        $query->setIntegrityCheck(false)
              ->from($this->_name, null)
              ->join('libraryTag', $this->_name.'.id = libraryTag.libraryId',array(
                                'libraryId',
                                'tagId'
                                ))
              ->join('Tags', 'libraryTag.tagId = Tags.tagId',array(
                                'name',
                                'libraryAmount'
                                ))
              ->where($this->getAdapter()->quoteInto($this->_name.'.id = ?', $libraryId));

        $res = $this->fetchAll($query);

        return $res;
    }
    
    public function incrementVote($id, $authorId)
    {
        $this->getAdapter()->beginTransaction();
        
        $data = array('vote' => new Zend_Db_Expr('vote + 1'));
        $this->update($data, array('id = ?' => $id));
        
        $query = $this->select()
                ->from($this->_name, array('vote', 'userId'))
                ->where($this->getAdapter()->quoteInto('id = ?',$id));
        $res = $this->fetchRow($query);
        
        if (!$res)
        {
            $this->getAdapter()->rollBack();
            throw new Exception("Document introuvable $id");
        }

        if($res->userId == $authorId)
        {
            $this->getAdapter()->rollBack();
            return false;
        }
        else
        {
            $this->getAdapter()->commit();
            return $res;
        }
    }

    public function decrementVote($id, $authorId)
    {
        $this->getAdapter()->beginTransaction();
        
        $data = array('vote' => new Zend_Db_Expr('vote - 1'));
        $this->update($data, array('id = ?' => $id));
        
        $query = $this->select()
                ->from($this->_name,array('vote', 'userId'))
                ->where($this->getAdapter()->quoteInto('id = ?', $id));
        $res = $this->fetchRow($query);
        
        if (!$res)
        {
            $this->getAdapter()->rollBack();
            throw new Exception("Document introuvable $id");
        }

        if($res->userId == $authorId)
        {
            $this->getAdapter()->rollBack();
            return false;
        }
        else
        {
            $this->getAdapter()->commit();
            return $res;
        }
    }
    
    public function alreadyFavorited($libraryId, $userId)
    {
        $query = $this->select()
                      ->setIntegrityCheck(false)
                      ->from($this->_name, null)
                      ->join('favoriteLibrary', 
                              $this->_name.'.id = favoriteLibrary.libraryId')
                      ->where($this->getAdapter()->quoteInto('favoriteLibrary.userId = ?', $userId))
                      ->where($this->getAdapter()->quoteInto('favoriteLibrary.libraryId = ?', $libraryId));
        $res = $this->fetchRow($query);
        if($res == null) {
            return false;
        }
        else {
            return true;
        }
    }
    
    public function getComments($id)
    {
        $query = $this->select();
        $query->setIntegrityCheck(false)
              ->from($this->_name, null)
              ->join('commentLibrary', $this->_name.'.id = commentLibrary.libraryId')
              ->join('user', 'commentLibrary.userId = user.id', 'login')
              ->where($this->getAdapter()->quoteInto($this->_name.'.id = ?', $id));

        $res = $this->fetchAll($query);
        return $res;
    }
    
    public function getDocumentsByTagName($name, $order = 'library.date DESC')
    {
        $query = $this->select();
        $query->setIntegrityCheck(false)
              ->from($this->_name, array(
                                'id',
                                'date',
                                'lastEditDate',
                                'title',
                                'content',
                                'public',
                                'flag',
                                'vote'
                                ))
              ->join('libraryTag', $this->_name.'.id = libraryTag.libraryId',array(
                                'libraryId',
                                'tagId'
                                ))
              ->join('Tags', 'libraryTag.tagId = Tags.tagId',array(
                                'name',
                                'libraryAmount'
                                ))
              ->join('user', $this->_name.'.userId = user.id', 'login')
              ->where('Tags.name = ?', $name)
              ->where($this->_name.'.public = 1')
              ->order($order)
              ->order($this->_name.'.date DESC');
        return $query;
    }
    
    public function isPublic($id)
    {
        $query = $this->select();
        $query->from($this->_name)
              ->where($this->getAdapter()->quoteInto($this->_name.'.id = ?', $id));
        $row = $this->fetchRow($query);
        if(intval($row->public) == 0)
            return false;
        else
            return true;
    }
    
    public function sortDocuments($sort_type, $tag_name = null)
    {
        $order = $this->_name.'.date DESC';
        switch($sort_type)
        {
            case 'votes': 
                $order = $this->_name.'.vote DESC';
                if($tag_name != null)
                    $res = $this->getDocumentsByTagName ($tag_name, $order);
                else
                    $res = $this->getAll($order);
                break;
        }
        
        return $res;
    }
    
    public function search($term)
    {
        $where = $this->getAdapter()->quoteInto('title LIKE ?', '%'.$term.'%');
        
        $query = $this->select();
        $query->setIntegrityCheck(false)
              ->from($this->_name, array(
                  'id',
                  'date',
                  'lastEditDate',
                  'title',
                  'content',
                  'public',
                  'flag',
                  'vote',
                  'userId'
                  ))
              ->join('user', $this->_name.'.userId = user.id', 'login')
              ->where('public = 1')
              ->where($where)
              ->orWhere($this->getAdapter()->quoteInto('content LIKE ?', '%'.$term.'%'))
              ->order($this->_name.'.date DESC');
        
        return $query;
    }
    
    public function getDocumentsAmount()
    {
        $query = $this->select()
                      ->from($this->_name, 'count(*) as amount')
                      ->where('public = 1');
        $res = $this->fetchRow($query);
        return $res->amount;
    }
}

