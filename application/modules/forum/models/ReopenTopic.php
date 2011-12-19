<?php

/**
 * Description of Tag
 *
 * @author jeremie
 */
class Forum_Model_ReopenTopic extends Zend_Db_Table_Abstract
{
    protected $_name = 'reopen_topic';
    
    protected $_referenceMap = array(
        'Topic' => array(
                'columns'       => array('topicId'),
                'refTableClass' => 'Forum_Model_Topic',
                'refColumns'    => array('topicId')
        ),
        'user'   => array(
                'columns'       => array('id'),
                'refTableClass' => 'Model_User',
                'refColumns'    => array('id')
        ));

    public function addRow($topic_id, $user_id)
    {
        $data = array(
            'topic_id' => $topic_id,
            'user_id' => $user_id
        );
        return $this->insert($data);
    }
    
    public function count($topic_id)
    {
        $select = $this->select()
                       ->from($this, array('count(*) as amount'))
                       ->where('topic_id = ?',$topic_id);;
        $row = $this->fetchRow($select);
       
        return intval($row->amount);
    }
    
    public function deleteByTopic($topic_id)
    {
        $where = $this->getAdapter()->quoteInto('topic_id = ?', $topic_id);
        $this->delete($where);
    }
}
?>
