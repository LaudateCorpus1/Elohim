<?php

/**
 * Description of Topic
 *
 * @author jeremie
 */
class Api_Model_Story extends Zend_Db_Table_Abstract {

    protected $_name = 'sahaba_story';
    private $_pageCount = 10;

    public function getById($id) {
        $id = (int) $id;
        $where = $this->getAdapter()->quoteInto('story_id = ?', $id);

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

    public function getStories($offset = 0, $name = null) {
        if ($offset == null || $offset < 0) {
            $offset = 0;
        }
        //$offset = $this->_pageCount * ($page - 1);
        $query = $this->select();
        $query->from($this->_name, array(
                    'id',
                    'text'
                ))
                ->order($this->_name . '.creation_date DESC')
                ->limit($this->_pageCount, $offset);

        if (!empty($name)) {
            $query->join('sahaba_story_link', $this->_name . '.id = sahaba_story_link.story_id', null)
                    ->join('sahaba', 'sahaba_story_link.sahaba_id = sahaba.id', array('name'))
                    ->where($this->getAdapter()->quoteInto('sahaba.name = ?', $name))
                    ->setIntegrityCheck(false);
        }

        return $this->fetchAll($query);
    }

    public function getSahabasArray($storyId) {
        $query = $this->select();
        $query->setIntegrityCheck(false)
                ->from($this->_name, null)
                ->join('sahaba_story_link', $this->_name . '.id = sahaba_story_link.story_id', array(
                    'story_id',
                    'sahaba_id'
                ))
                ->join('sahaba', 'sahaba_story_link.sahaba_id = sahaba.id', array(
                    'name'
                ))
                ->where($this->getAdapter()->quoteInto($this->_name . '.id = ?', $storyId));

        $res = $this->fetchAll($query);

        $sahabasArray = array();
        foreach ($res as $sahaba) {
            $sahabasArray[] = $sahaba->name;
        }

        return $sahabasArray;
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
