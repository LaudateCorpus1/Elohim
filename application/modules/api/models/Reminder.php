<?php

/**
 * Description of Topic
 *
 * @author jeremie
 */
class Api_Model_Reminder extends Zend_Db_Table_Abstract {

    protected $_name = 'reminder';
    private $_pageCount = 10;

    public function getById($id) {
        $id = (int)$id;
        $where = $this->getAdapter()->quoteInto('id = ?', $id);

        $query = $this->select();
        $query->from($this->_name)
              ->join('reminder_category', $this->_name.'.category_id = reminder_category.id', array('category' => 'name'))
              ->where($where);
        
        $row = $this->fetchRow($query);
        if (!$row) {
            throw new Exception("Rappel introuvable : $id");
        }
        return $row;
    }

    public function getReminders($offset = 0) {
        if ($offset == null || $offset < 0) {
            $offset = 0;
        }
        //$offset = $this->_pageCount * ($page - 1);
        $order = $this->_name.'.creation_date DESC';
        $query = $this->select();
        $query->from($this->_name)
              ->setIntegrityCheck(false)
              ->join('reminder_category', $this->_name.'.category_id = reminder_category.id', array('category' => 'name'))
              ->order($order)
              ->limit($this->_pageCount, $offset);
        
        return $this->fetchAll($query);
    }

    public function add($title, $text, $categoryId) {
        $reminder = array(
            'title' => $title,
            'text' => $text,
            'category_id' => $categoryId,
            'creation_date' => gmdate('Y-m-d H:i:s', time())
        );
        return $this->insert($reminder);
    }

    public function deleteStory($id) {
        $this->delete(array('id = ?' => $id));
    }

    public function updateStory(array $data, $id) {
        $this->update($data, array('id = ?' => $id));
    }
}

?>
