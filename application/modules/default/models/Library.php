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
    
    public function getByUsername($username)
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
        
        return $query;
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
}

