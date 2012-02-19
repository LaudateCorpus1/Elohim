<?php

class Default_Model_Comment extends Zend_Db_Table_Abstract
{
    protected $_name = 'comment';
    
    public function get($id)
    {
        $id = (int)$id;
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        $row = $this->fetchRow($this->select()->where($where));
        if($row == null)
        {
                throw new Exception("Commentaire introuvable : $id");
        }
        return $row;
    }
    
    public function getAll()
    {
        return $this->fetchAll();
    }
    
    public function insertComment($author, $content, $article_id)
    {
        $date = time();
        $data = array(
            'author' => $author,
            'content' => $content,
            'id_article' => $article_id,
            'date_posted' => $date
        );
        return $this->insert($data);
    }

    public function deleteComment($id)
    {
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        $this->delete($where);
    }

    public function updateComment(array $data, $id)
    {
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        $this->update($data, $where);
    }
}

