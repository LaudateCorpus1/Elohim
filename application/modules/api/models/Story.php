<?php

/**
 * Description of Topic
 *
 * @author jeremie
 */
class Api_Model_Story extends Zend_Db_Table_Abstract {

    protected $_name = 'sahaba_story';

    public function getStory($id) {
        $id = (int) $id;
        $where = $this->getAdapter()->quoteInto('storyId = ?', $id);

        $query = $this->select();
        $query->setIntegrityCheck(false)
                ->from($this->_name, array(
                    'text',
                    'creation_date',
                    'storyId' => 'id'
                ))
                ->join('sahaba_story_link', 'sahaba_story.id = sahaba_story_link.story_id'/* , array(
                          'topicTag_topicId' => 'topicId',
                          'topicTag_tagId' => 'tagId'
                          ) */)
                ->join('sahaba', 'sahaba_story_link.sahaba_id = sahaba.id', array(
                    'sahabaId' => 'id',
                    'name'
                ))
                ->where($where);


        $row = $this->fetchRow($query);
        if (!$row) {
            throw new Exception("Story introuvable : $id");
        }
        return $row;
    }

    public function getWithLimit($limit = 10, $order = 'sahaba_story.creation_date DESC') {
        $query = $this->select();
        $query->from($this->_name, array(
                    'id',
                    'text'
                ))
                ->order($order)
                ->limit($limit);

        return $this->fetchAll($query);
    }
    
    public function getSahabas($storyId)
    {
        $query = $this->select();
        $query->setIntegrityCheck(false)
              ->from($this->_name, null)
              ->join('sahaba_story_link', $this->_name.'.id = sahaba_story_link.story_id',array(
                                'story_id',
                                'sahaba_id'
                                ))
              ->join('sahaba', 'sahaba_story_link.sahaba_id = sahaba.id',array(
                                'name'
                                ))
              ->where($this->getAdapter()->quoteInto($this->_name.'.id = ?', $storyId));

        $res = $this->fetchAll($query);

        return $res;
    }

    public function addStory($text) {
        $story = array(
            'text' => $text,
            'creation_date' => gmdate('Y-m-d H:i:s', time())
        );
        return $this->insert($story);
    }

    public function deleteStory($id) {
        $this->delete(array('id = ?' => $id));
    }

    public function updateStory(array $data, $id) {
        $this->update($data, array('id = ?' => $id));
    }
}

?>
