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
class Forum_Model_Vote extends Zend_Db_Table_Abstract
{
    protected $_name = 'vote';

   public function addVote($userId, $messageId, $type)
   {
       $data = array(
           'userId' => $userId,
           'messageId' => $messageId,
           'type' => $type
       );
       $this->insert($data);
   }
   
   public function deleteVote($userId, $messageId, $type)
   {
       $this->delete(array('userId = ?' => $userId, 'messageId = ?' => $messageId, 'type LIKE ?' => '%'.$type));
   }
   
   public function alreadyVoted($userId, $messageId, $type)
   {
       $query = $this->select()
                      ->from($this->_name)
                      ->where($this->getAdapter()->quoteInto('userId = ?', $userId))
                      ->where($this->getAdapter()->quoteInto('messageId = ?', $messageId))
                      ->where($this->getAdapter()->quoteInto('type = ?', $type));
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
}
?>
