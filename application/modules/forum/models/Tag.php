<?php

/**
 * Description of Tag
 *
 * @author jeremie
 */
class Forum_Model_Tag extends Zend_Db_Table_Abstract
{
    protected $_name = 'Tags';

    public function getTag($id)
    {
            $id = (int)$id;
            $row = $this->fetchRow(array('tagId = ?' => $id));
            if (!$row)
            {
                throw new Exception("Tag introuvable $id");
            }
            return $row->toArray();
    }
    
    public function getAll($count = 50)
    {
        $query = $this->select();
        $query->from($this->_name, array(
                  'amount',
                  'name',
                  'tagId'
                  ))
              ->where('amount != 0')
              ->order('amount DESC')
              ->limit($count);
        
        $res = $this->fetchAll($query);
        return $res;
    }

    public function addTag($name, $amount, $amountColumn = 'amount')
    {
        $data = array(
            'name' => $name,
            $amountColumn => $amount
        );
        return $this->insert($data);
    }

    public function deleteTag($id)
    {
        $this->delete(array('tagId = ?' => $id));
    }

    public function updateTag(array $data, $id)
    {
        $this->update($data, array('tagId = ?' => $id));
    }

    public function incrementTag($name, $column = 'amount')
    {
        $query = $this->select()
                ->from($this->_name,array('tagId', $column))
                ->where('name = ?',$name);
        $res = $this->fetchRow($query);
        if (!$res)
        {
            throw new Exception("Tag introuvable $name");
        }
        $amount = (int)$res->$column + 1;
        $data = array($column => $amount);
        $this->update($data, $this->getAdapter()->quoteInto('name = ?', (string)$name));
        return $res->tagId;
    }
    
    public function decrementTag($name, $column = 'amount')
    {
        $query = $this->select()
                ->from($this->_name,array('tagId', $column))
                ->where('name = ?',$name);
        $res = $this->fetchRow($query);
        if (!$res)
        {
            throw new Exception("Tag introuvable $name");
        }
        $amount = (int)$res->$column - 1;
        $data = array($column => $amount);
        $this->update($data, $this->getAdapter()->quoteInto('name = ?', (string)$name));
        return $res->tagId;
    }

    /*
     * Retourne false si le tag n'existe pas, son ID sinon
     */
    public function doesExist($tag)
    {
        $query = $this->select();
        $query->from($this->_name)
              ->where('name = ?',$tag);
        $res = $this->fetchRow($query);
        if($res == null)
        {
            return false;
        }
        else
        {
            return $res->tagId;
        }
    }

    public function getTagName($id)
    {
        $query = $this->select()
                      ->from($this->_name,'name')
                      ->where('tagId = ?',(int)$id);
        $res = $this->fetchRow($query);
        if (!$res)
        {
            throw new Exception("Tag introuvable $id");
        }
        return $res->name;
    }
    
    public function getIds(array $names)
    {
        if(count($names) != 0)
        {
            $query = $this->select()
                          ->from($this->_name,'tagId')
                          ->where('name IN(?)', $names);
            $res = $this->fetchAll($query);
            return $res->toArray();
        }
        else
            return null;
    }

    public function getFavoriteTags($userId)
    {
        $query = $this->select()
                      ->setIntegrityCheck(false)
                      ->from($this->_name,array(
                                        'tags_tagId' => 'tagId',
                                        'name'
                                        ))
                      ->join('FavoritesTags', 'Tags.tagId = FavoritesTags.tagId',array(
                                        'FavoritesTags_tagId' => 'tagId'
                                        ))
                      ->where('FavoritesTags.userId = ?',$userId);
//                      ->order('id ASC');

        $res = $this->fetchAll($query);

        return $res;
    }

    public function alreadyFavorited($tagId, $userId)
    {
        $query = $this->select()
                      ->setIntegrityCheck(false)
                      ->from($this->_name,array(
                                        'tags_tagId' => 'tagId'
                                        ))
                      ->join('FavoritesTags', 'Tags.tagId = FavoritesTags.tagId',array(
                                        'FavoritesTags_tagId' => 'tagId'
                                        ))
                      ->where('FavoritesTags.userId = ?',$userId)
                      ->where('FavoritesTags.tagId = ?',$tagId);
        $res = $this->fetchRow($query);
        if($res == null) {
            return false;
        }
        else {
            return true;
        }
    }

    public function search($term)
    {
        $filter = function($tag) use ($term)
        {
            $tags = explode(" ", $term);
            $finalterm = end($tags);
            if(stristr($tag, $finalterm))
                    return true;
            return false;
        };
        $t = array();
        $taglist = $this->fetchAll();
        foreach($taglist as $tag)
        {
            $t[] = $tag->name;
        }
        return array_filter($t,$filter);
    }
}
?>
