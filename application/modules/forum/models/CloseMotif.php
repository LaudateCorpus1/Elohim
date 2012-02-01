<?php

/**
 * Description of Tag
 *
 * @author jeremie
 */
class Forum_Model_CloseMotif extends Zend_Db_Table_Abstract
{
    protected $_name = 'close_motif';

    public function addMotif($topic_id, $motif, $user_id)
    {
        $data = array(
            'topic_id' => $topic_id,
            'motif' => $motif,
            'user_id' => $user_id
        );
        return $this->insert($data);
    }
    
    public function countMotif($topic_id)
    {
        $select = $this->select()
                       ->from($this, array('count(*) as amount'))
                       ->where($this->getAdapter()->quoteInto('topic_id = ?',$topic_id));
        $row = $this->fetchRow($select);
       
        return intval($row->amount);
    }
    
    public function deleteByTopic($topic_id)
    {
        $where = $this->getAdapter()->quoteInto('topic_id = ?', $topic_id);
        $this->delete($where);
    }
    
    public function hasAlreadyVoted($user_id, $topic_id)
    {
        $select = $this->select()
                       ->from($this->_name)
                       ->where($this->getAdapter()->quoteInto('topic_id = ?',$topic_id))
                       ->where($this->getAdapter()->quoteInto('user_id = ?',$user_id));
        $row = $this->fetchRow($select);
        if(!$row)
            return false;
        else
            return true;
    }
}
?>
