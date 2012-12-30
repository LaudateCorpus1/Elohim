<?php

class Model_News extends Zend_Db_Table_Abstract
{
    protected $_name = 'news';
    
    public function get($id)
    {
        $id = (int)$id;
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        $row = $this->fetchRow($this->select()->where($where));
        if($row == null)
        {
                throw new Exception("News introuvable : $id");
        }
        return $row;
    }
    
    public function getAll()
    {
        $query = $this->select();
        $query->setIntegrityCheck(false)
              ->from($this->_name, array(
                  'id',
                  'date_posted',
                  'last_updated',
                  'title',
                  'content',
                  'author'
                  ))
              ->order('date_posted  DESC');
        
        return $query;
    }
    
    public function getLastNews($count = 5)
    {
        return $this->fetchAll(null, 'date_posted  DESC', $count);
    }
    
    public function addNews($author, $title, $content)
    {
        $data = array(
            'author' => $author,
            'title' => $title,
            'content' => $content,
            'date_posted' => gmdate('Y-m-d H:i:s', time())
        );
        return $this->insert($data);
    }
    
    public function updateNews(array $data, $id)
    {
        $this->update($data, $this->getAdapter()->quoteInto('id = ?', $id));
    }
    
    public function deleteNews($id)
    {
        $this->delete(array('id = ?' => $id));
    }
}

