<?php

class Default_Model_Library extends Zend_Db_Table_Abstract
{
    protected $_name = 'library';
    protected $_searchIndexerClass;
    
    function __construct($config = array()) 
    {
        $this->_searchIndexerClass = new Default_Model_LibraryRow();
        parent::__construct($config);
    }
    
    public function get($id)
    {
        $where = $this->getAdapter()->quoteInto($this->_name.'.id = ?', $id);
        $query = $this->select();
        $query->setIntegrityCheck(false)
              ->from($this->_name)
              ->join('user', $this->_name.'.userId = user.id', 'login')
              ->join('category', $this->_name.'.categoryId = category.id', array('category' => 'name'))
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
                  'key' => 'id',
                  'date',
                  'lastEditDate',
                  'title',
                  'content',
                  'public',
                  'flag',
                  'vote',
                  'userId',
                  'categoryId'
                  ))
              ->join('user', $this->_name.'.userId = user.id', 'login')
              ->join('category', $this->_name.'.categoryId = category.id', array('category' => 'name'))
              ->where('public = 1')
              ->order($order)
              ->order($this->_name.'.date DESC');
        
        return $query;
    }
    
    public function getDocumentsByCategory($categoryId, $order = 'library.date DESC')
    {
        $query = $this->select();
        $query->setIntegrityCheck(false)
              ->from($this->_name, array(
                  'key' => 'id',
                  'date',
                  'lastEditDate',
                  'title',
                  'content',
                  'public',
                  'flag',
                  'vote',
                  'userId',
                  'categoryId'
                  ))
              ->join('user', $this->_name.'.userId = user.id', 'login')
              ->join('category', $this->_name.'.categoryId = category.id', array('category' => 'name'))
              ->where('public = 1')
              ->where($this->getAdapter()->quoteInto($this->_name.'.categoryId = ?', $categoryId))
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
    
    public function addDocument($userId, $title, $content, $public, $source, $categoryId)
    {
        $data = array(
            'userId' => $userId,
            'title' => $title,
            'content' => $content,
            'public' => $public,
            'source' => $source,
            'vote' => '0',
            'categoryId' => $categoryId
        );
        $id = $this->insert($data);
        
        if($public == '1')
        {
            $modelUser = new Model_User();
            $login = $modelUser->getLoginById($userId);
            $data['id'] = $id;
            $data['login'] = $login;
            $data['class'] = 'Library';
            $this->_searchIndexerClass->notify('insert', $data);
        }
        return $id;
    }
    
    public function updateDocument(array $data, $id)
    {
        $search = $data;
        if(isset($data['login']))
            unset($data['login']);
        $nb = $this->update($data, $this->getAdapter()->quoteInto('id = ?', $id));
        if($nb == 1 && $data['public'] == '1')
        {
            $search['id'] = $id;
            $search['class'] = 'Library';
            $this->_searchIndexerClass->notify('update', $search);
        }
        else if($data['public'] == '0')
        {
            $search['id'] = $id;
            $search['class'] = 'Library';
            $this->_searchIndexerClass->notify('delete', $search);
        }
    }
    
    public function deleteDocument($id)
    {
        $this->delete(array('id = ?' => $id));
        $luceneData = array(
            'class' => 'Library',
            'id' => $id,
            'title' => '',
            'vote' => '',
            'content' => '',
            'date' => '',
            'userId' => '',
            'login' => ''
        );
        $this->_searchIndexerClass->notify('delete', $luceneData);
    }
    
    public function getByUsername($username, $flagOwner = false)
    {
        $modelUser = new Model_User();
        if($modelUser->doesExist($username))
        {
            $query = $this->select();
            $query->setIntegrityCheck(false)
                  ->from($this->_name, array(
                      'key' => 'id',
                      'date',
                      'lastEditDate',
                      'title',
                      'content',
                      'public',
                      'flag',
                      'vote',
                      'categoryId'
                      ))
                  ->join('user', $this->_name.'.userId = user.id', null)
                  ->join('category', $this->_name.'.categoryId = category.id', array('category' => 'name'))
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
    
    public function getDocumentsByTagName($name, $order = 'library.date DESC', $categoryId = null)
    {
        $query = $this->select();
        $query->setIntegrityCheck(false)
              ->from($this->_name, array(
                                'key' => 'id',
                                'date',
                                'lastEditDate',
                                'title',
                                'content',
                                'public',
                                'flag',
                                'vote',
                                'userId',
                                'categoryId'
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
              ->join('category', $this->_name.'.categoryId = category.id', array('category' => 'name'))
              ->where('Tags.name = ?', $name)
              ->where($this->_name.'.public = 1')
              ->order($order)
              ->order($this->_name.'.date DESC');
        
        if($categoryId != null && $categoryId != '')
            $query->where($this->getAdapter()->quoteInto($this->_name.'.categoryId = ?', $categoryId));
              
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
    
    public function sortDocuments($sort_type, $tag_name = null, $categoryId = null)
    {
        $order = $this->_name.'.date DESC';
        switch($sort_type)
        {
            case 'votes': 
                $order = $this->_name.'.vote DESC';
                if($tag_name != null)
                    $res = $this->getDocumentsByTagName($tag_name, $order, $categoryId);
                else if($categoryId != null)
                    $res = $this->getDocumentsByCategory ($categoryId);
                else
                    $res = $this->getAll($order);
                break;
        }
        
        return $res;
    }
    
    public function search($term)
    {
        $search = Zend_Registry::get('search');
        $index = Zend_Search_Lucene::open($search->getIndexDirectory());
        $term = trim($term);
        if(strpos($term, ' ') === false)
        {
            $qTerm = new Zend_Search_Lucene_Index_Term($term);
            $query = new Zend_Search_Lucene_Search_Query_Term($qTerm);
        }
        else
        {
            $query = new Zend_Search_Lucene_Search_Query_MultiTerm();
            $terms = explode(' ', $term);
            foreach($terms as $word)
            {
                $query->addTerm(new Zend_Search_Lucene_Index_Term($word), true);
            }
        }
        
        $hits = $index->find($query);
        if(count($hits) > 0)
            return $hits;
        else
            return null;
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

