<?php

class Model_Article extends Zend_Db_Table_Abstract
{
    protected $_name = 'article';
    
    public function get($id)
    {
        $id = (int)$id;
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        $row = $this->fetchRow($this->select()->where($where));
        if($row == null)
                throw new Exception("Article introuvable");
        return $row;
    }
    
    public function getAll()
    {
        return $this->fetchAll();
    }
    
    public function getByCategory($id_category)
    {
        $rows = $this->fetchAll($this->select()->where('id_category = ?', $id_category));
        if($rows == null)
                throw new Exception("Article introuvable");
        return $rows;
    }
    
    public function getComments($article_id)
    {
        $query = $this->select();
        $query->setIntegrityCheck(false)
              ->from($this->_name,array(
                                'article_id' => 'id'
                                ))
              ->join('comment', $this->_name.'.id = comment.id_article',array(
                                'comment_id' => 'id',
                                'author',
                                'content',
                                'date_posted'
                                ))
              ->where($this->_name.'.id = ?',$article_id);

        $res = $this->fetchAll($query);
        return $res;
    }
    
    
    public function addArticle($title, $content, $category_id)
    {
            $data = array(
                'title' => $title,
                'content' => $content,
                'id_category' => $category_id,
                'date_posted' => time()
            );
            return $this->insert($data);
    }

    public function deleteArticle($id)
    {
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        $this->delete($where);
    }

    public function updateArticle(array $data, $id)
    {
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        $this->update($data, $where);
    }
}

