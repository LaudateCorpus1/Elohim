<?php
/**
 * Description of Message
 *
 * @author jeremie
 */
class Forum_Model_Message extends Zend_Db_Table_Abstract
{
    protected $_name = 'Messages';

    public function getMessage($id)
    {
            $id = (int)$id;
            $row = $this->fetchRow(array('messageId = ?' => $id));
            if (!$row)
            {
                    throw new Exception("Could not find row $id");
            }
            return $row->toArray();
    }
    
    public function getAuthor($id)
    {
        $query = $this->select()
                ->from($this->_name, 'userId')
                ->where($this->getAdapter()->quoteInto('messageId = ?',$id));
        $row = $this->fetchRow($query);
        
        if (!$row)
        {
                throw new Exception("Could not find row $id");
        }
        return $row;
    }

    public function addMessage($userId, $topicId, $content, $ipAddress = null, $date = null, $vote = 0)
    {
            $data = array(
                'userId' => $userId,
                'topicId' => $topicId,
                'content' => $content,
                'date' => $date,
                'vote' => $vote,
                'ipAddress' => $ipAddress
            );
            return $this->insert($data);
    }

    public function deleteMessage($id)
    {
        $this->delete(array($this->getAdapter()->quoteInto('messageId = ?', $id)));
    }

    public function updateMessage(array $data, $id)
    {
            $this->update($data, array($this->getAdapter()->quoteInto('messageId = ?', $id)));
    }

    public function incrementVote($messageId, $author_id)
    {
        $this->getAdapter()->beginTransaction();
        
        $data = array('vote' => new Zend_Db_Expr('vote + 1'));
        $this->update($data, array('messageId = ?' => $messageId));
        
        $query = $this->select()
                ->from($this->_name, array('vote', 'userId'))
                ->where($this->getAdapter()->quoteInto('messageId = ?',$messageId));
        $res = $this->fetchRow($query);

        if($res->userId == $author_id)
        {
            $this->getAdapter()->rollBack();
            return false;
        }
        else
        {
            $this->getAdapter()->commit();
            return $res;
        }
    }

    public function decrementVote($messageId, $author_id)
    {
        $this->getAdapter()->beginTransaction();
        
        $data = array('vote' => new Zend_Db_Expr('vote - 1'));
        $this->update($data, array('messageId = ?' => $messageId));
        
        $query = $this->select()
                ->from($this->_name,array('vote', 'userId'))
                ->where($this->getAdapter()->quoteInto('messageId = ?',$messageId));
        $res = $this->fetchRow($query);

        if($res->userId == $author_id)
        {
            $this->getAdapter()->rollBack();
            return false;
        }
        else
        {
            $this->getAdapter()->commit();
            return $res;
        }
    }

    public function getCommentsFromMessage($messageId)
    {
        $query = $this->select();
        $query->setIntegrityCheck(false)
              ->from($this->_name,array(
                                'message_messageId' => 'messageId'
                                ))
              ->join('CommentMessage', 'Messages.messageId = CommentMessage.messageId',array(
                                'commentMessage_messageId' => 'messageId',
                                'commentMessage_commentId' => 'commentId'
                                ))
              ->join('Comments', 'CommentMessage.commentId = Comments.commentId',array(
                                'comment_commentId' => 'commentId',
                                'userId',
                                'content',
                                'date'
                                ))
              ->join('user', 'Comments.userId = user.id', array('login'))
              ->where('Messages.messageId = ?',$messageId);

        $res = $this->fetchAll($query);

        return $res;
    }
}
?>
