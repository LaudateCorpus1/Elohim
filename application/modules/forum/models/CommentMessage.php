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
class Forum_Model_CommentMessage extends Zend_Db_Table_Abstract
{
    protected $_name = 'CommentMessage';

    protected $_referenceMap = array(
        'Comment' => array(
                'columns'       => array('commentId'),
                'refTableClass' => 'Model_Comment',
                'refColumns'    => array('commentId')
        ),
        'Message'   => array(
                'columns'       => array('messageId'),
                'refTableClass' => 'Message_Tag',
                'refColumns'    => array('messageId')
        ));

   public function addRow($commentId,$messageId)
   {
       $data = array(
           'commentId' => $commentId,
           'messageId'   => $messageId
       );
       $this->insert($data);
   }
}
?>
