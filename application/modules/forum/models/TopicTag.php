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
}
?>
