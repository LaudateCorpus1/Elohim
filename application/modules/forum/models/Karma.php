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
class Forum_Model_Karma extends Zend_Db_Table_Abstract
{
   protected $_name = 'karma';

   public function addKarmaAction(array $data)
   {
       /*$lastAction = null;
       switch ($date['type'])
       {
           case 'DOWN_MESSAGE':
           case 'UP_MESSAGE':
               $lastAction = $this->getLastAction(array('fromUserId' => $data['fromUserId'], 'messageId' => $data['messageId']));
               break;
               
       }
       
       if($lastAction == null)
           $data['cancellation'] = false;
       else
       {
           // Si l'action demandée est le contraire de la dernière action, c'est une annulation
           if($lastAction->type != $data['type'])
               $data['cancellation'] = true;
           // Si l'action demandée est la meme que la dernière et que celle-ci était une annulation
           elseif($lastAction->type == $data['type'] && $lastAction->cancellation == true)
               $data['cancellation'] = false;
           // Si l'action demandée est la meme que la dernière et que celle-ci n'était pas une annulation... impossible, cheater !
           elseif($lastAction->type == $data['type'] && $lastAction->cancellation == false)
               return false;
       }*/
       
       $this->insert($data);
       
       //return true;
   }
   
   /*
    * @param where : clause WHERE sous la forme key => value (columnName => value)
    */
   public function getLastAction(array $where)
   {
       $query = $this->select()
                      ->from($this->_name)
                      ->order('date DESC')
                      ->limit(1);
       
       foreach($where as $key => $value)
           $query->where($this->getAdapter()->quoteInto($key.' = ?', $value));
       
        $res = $this->fetchRow($query);
        return $res;
   }
   
   /*public function deleteVote($userId, $messageId, $type)
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
   }*/
}
?>
