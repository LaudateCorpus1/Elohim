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
       $this->insert($data);
   }
   
   /*
    * @param where : clause WHERE sous la forme key => value (columnName => value)
    */
   public function getLastAction(array $where)
   {
       $query = $this->select()
                      ->from($this->_name)
                      ->order('date DESC')
                      ->order('id DESC')
                      ->limit(1);
       
       foreach($where as $key => $value)
           $query->where($this->getAdapter()->quoteInto($key.' = ?', $value));
       
        $res = $this->fetchRow($query);
        return $res;
   }
   
   public function getTodayVotesCastByUser($userId)
   {
       $query = $this->select()
                      ->from($this->_name, 'count(*) as nbVotesCast')
                      ->where($this->getAdapter()->quoteInto('fromUserId = ?', $userId))
                      ->where('date LIKE \''.date('Y-m-d%\''))
                      ->where('cancellation = 0')
                      ->group('fromUserId')
                      ->limit(1);
       
        $res = $this->fetchRow($query);
        if($res == null)
            return 0;
        return intval($res->nbVotesCast);
   }
   
   public function getTopicVotesByUser($userId, $topicId)
   {
   }
}
?>
