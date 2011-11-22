<?php

/**
 * Description of TopicTag
 *
 * @author jeremie
 */
class Forum_Model_TopicTag extends Zend_Db_Table_Abstract
{
    protected $_name = 'TopicTag';

    protected $_referenceMap = array(
        'Topic' => array(
                'columns'       => array('topicId'),
                'refTableClass' => 'Forum_Model_Topic',
                'refColumns'    => array('topicId')
        ),
        'Tag'   => array(
                'columns'       => array('tagId'),
                'refTableClass' => 'Forum_Model_Tag',
                'refColumns'    => array('tagId')
        ));

   public function addRow($topicId,$tagId)
   {
       $data = array(
           'topicId' => $topicId,
           'tagId'   => $tagId
       );
       $this->insert($data);
   }
   
   public function deleteRow($topicId, $tagId)
   {
       $where = array();
       $where[] = $this->getAdapter()->quoteInto('topicId = ?', $topicId);
       $where[] = $this->getAdapter()->quoteInto('tagId = ?', $tagId);
       $this->delete($where);
   }
   
    public function addMultipleTags($topicId, array $tags)
    {
        if(count($tags) != 0)
        {
            $data = array(
                   'topicId' => $topicId
                );
            foreach ($tags as $tag)
            {
                $data['tagId'] = $tag;
                $this->insert($data);
            }
        }
    }
    
    public function deleteMultipleTags($topicId, array $tags)
    {
        if(count($tags) != 0)
        {
            $where = array();
            $where[] = $this->getAdapter()->quoteInto('topicId = ?', $topicId);
            $where[] = $this->getAdapter()->quoteInto('tagId IN(?)', $tags);
            $this->delete($data, $where);
        }
    }
   
    public function updateRow(array $data, $topic_id, $tag_id)
    {
        $where = array();
        $where[] = $this->getAdapter()->quoteInto('topicId = ?', $topic_id);
        $where[] = $this->getAdapter()->quoteInto('tagId = ?', $tag_id);
        $this->update($data, $where);
    }
    
    public function deleteByTopicId($topic_id) 
    {
        $where = $this->getAdapter()->quoteInto('topicId = ?', $id);
        $this->delete($where);
    }
}
?>
