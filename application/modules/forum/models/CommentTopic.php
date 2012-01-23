<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CommentMessage
 *
 * @author jeremie
 */
class Forum_Model_CommentTopic extends Zend_Db_Table_Abstract
{
    protected $_name = 'commentTopic';

    protected $_referenceMap = array(
        'Comment' => array(
                'columns'       => array('commentId'),
                'refTableClass' => 'Forum_Model_Comment',
                'refColumns'    => array('commentId')
        ),
        'Topic'   => array(
                'columns'       => array('topicId'),
                'refTableClass' => 'Forum_Model_Topic',
                'refColumns'    => array('topicId')
        ));

   public function addRow($commentId, $topicId)
   {
       $data = array(
           'commentId' => $commentId,
           'topicId'   => $topicId
       );
       $this->insert($data);
   }
}
?>
