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
                    throw new Exception("Could not find row $id");
            }
            return $row->toArray();
    }

    public function addTag($name, $amount)
    {
            $data = array(
                'name' => $name,
                'amount' => $amount
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

    public function incrementTag($name)
    {
        $query = $this->select()
                ->from($this->_name,array('tagId','amount'))
                ->where('name = ?',$name);
        $res = $this->fetchRow($query);
        $amount = (int)$res->amount + 1;
        $data = array('amount' => $amount);
        $this->update($data, $this->getAdapter()->quoteInto('name = ?', (string)$name));
        return $res->tagId;
    }

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
            return true;
        }
    }

    public function getTagName($id)
    {
        $query = $this->select()
                      ->from($this->_name,'name')
                      ->where('tagId = ?',(int)$id);
        $res = $this->fetchRow($query);
        return $res->name;
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
        if($res == null)
        {
                return false;
        }
        else
        {
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
