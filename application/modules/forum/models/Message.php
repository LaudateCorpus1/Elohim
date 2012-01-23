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
            return $row;
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

    public function updateMessage(array $data, $messageId, $topicId = null)
    {
        $data['lastActivity'] = date('Y-m-d H:i:s', time());
        $where = array();
        $where[] = $this->getAdapter()->quoteInto('messageId = ?', $messageId);
        if($topicId != null)
            $where[] = $this->getAdapter()->quoteInto('topicId = ?', $topicId);
        return $this->update($data, $where);
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
              ->from($this->_name,'messageId')
              ->join('CommentMessage', 'Messages.messageId = CommentMessage.messageId', null)
              ->join('Comments', 'CommentMessage.commentId = Comments.commentId',array(
                                'commentId',
                                'userId',
                                'content',
                                'date'
                                ))
              ->join('user', 'Comments.userId = user.id', array('login'))
              ->where('Messages.messageId = ?',$messageId);

        $res = $this->fetchAll($query);

        return $res;
    }
    
    public function sortMessages($topicId, $sort_type)
    {
        $order = null;
        $model_topic = new Forum_Model_Topic();
        
        switch($sort_type)
        {
            case 'votes': 
                $order = $this->_name.'.vote DESC';
                break;
                
            case 'activity':
                $order = $this->_name.'.lastActivity DESC';
                break;
        }
        
        $messages = $model_topic->getMessagesFromTopic($topicId, $order);
        return $messages;
    }
}
?>
