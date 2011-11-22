<?php
/**
 * Description of Topic
 *
 * @author jeremie
 */
class Forum_Model_Topic extends Zend_Db_Table_Abstract
{
    protected $_name = 'Topic';

    public function getTopic($id)
    {
            $id = (int)$id;
            $row = $this->fetchRow(array('topicId = ?' => $id));
            if (!$row)
            {
                    throw new Exception("Could not find row $id");
            }
            return $row->toArray();
    }
    
    public function getAll($closed_flag = true, $count = 50, $order = 'Topic.date DESC')
    {
        $orderby = $order;
        $query = $this->select();
        $query->setIntegrityCheck(false)
              ->from($this->_name, array(
                  'count(Messages.topicId) as amount_messages',
                  'topicId',
                  'title',
                  'userId',
                  'date',
                  'vote',
                  'status'
                  ))
              ->joinLeft(array('Messages'), 'Topic.topicId=Messages.topicId', null)
              ->group($this->_name.'.topicId')
              ->order($orderby)
              ->order('Topic.date DESC')
              ->limit($count);
        
        if(!$closed_flag)
            $query->where('status != "closed"');

        $res = $this->fetchAll($query);
        return $res;
    }

    public function addTopic($userId, $title, $message, $ipAddress = null, $date = null, $vote = 0)
    {
            $data = array(
                'userId' => $userId,
                'title' => $title,
                'message' => $message,
                'date' => $date,
                'vote' => $vote,
                'ipAddress' => $ipAddress
            );
            return $this->insert($data);
    }

    public function deleteTopic($id)
    {
        $this->delete(array('topicId = ?' => $id));
    }

    public function updateTopic(array $data, $id)
    {
            $this->update($data, array('topicId = ?' => $id));
    }

    public function getMessagesFromTopic($topicId)
    {
        $topicId = (int)$topicId;
        $query = $this->select();
        $query->setIntegrityCheck(false)
              ->from($this->_name,array(
                                'topic_topicId' => 'topicId'
                                ))
              ->join(array('Messages'), 'Topic.topicId=Messages.topicId',array(
                                'messageId',
                                'messages_userId' => 'userId',
                                'messages_topicId' => 'topicId',
                                'content',
                                'messages_date' => 'date',
                                'messages_vote' => 'vote'
                                ))
              ->join(array('user'), 'Messages.userId=user.id', 'login') 
              ->where('Topic.topicId = ?',$topicId)
              ->order('Messages.date ASC');

        $res = $this->fetchAll($query);
 
        return $res;
    }

    public function getTagsFromTopic($topicId)
    {
        $query = $this->select();
        $query->setIntegrityCheck(false)
              ->from($this->_name,array(
                                'topic_topicId' => 'topicId'
                                ))
              ->join('TopicTag', 'Topic.topicId = TopicTag.topicId',array(
                                'topicTag_topicId' => 'topicId',
                                'topicTag_tagId' => 'tagId'
                                ))
              ->join('Tags', 'TopicTag.tagId = Tags.tagId',array(
                                'tag_tagId' => 'tagId',
                                'name',
                                'amount'
                                ))
              ->where('Topic.topicId = ?',$topicId);

        $res = $this->fetchAll($query);

        return $res;
    }

    public function getTopicsByTagName($name, $closed_flag = true, $count = 50, $order = 'Topic.date DESC')
    {
        $orderby = $order;
        
        $query = $this->select();
        $query->setIntegrityCheck(false)
              ->from($this->_name,array(
                                'count(Messages.topicId) as amount_messages',
                                'topicId',
                                'userId',
                                'title',
                                'message',
                                'date',
                                'vote',
                                'status'
                                ))
              ->joinLeft('Messages', 'Topic.topicId=Messages.topicId', null)
              ->join('TopicTag', 'Topic.topicId = TopicTag.topicId',array(
                                'topicTag_topicId' => 'topicId',
                                'topicTag_tagId' => 'tagId'
                                ))
              ->join('Tags', 'TopicTag.tagId = Tags.tagId',array(
                                'tag_tagId' => 'tagId'
                                ))
              ->where('Tags.name = ?',$name)
              ->group($this->_name.'.topicId')
              ->order($orderby)
              ->order('Topic.date DESC')
              ->limit($count);
        
        if(!$closed_flag)
            $query->where('status != "closed"');

        $res = $this->fetchAll($query);
        return $res;
    }

    public function incrementVote($topicId)
    {
        $query = $this->select()
                ->from($this->_name,'vote')
                ->where('topicId = ?',$topicId);
        $res = $this->fetchRow($query);

        $data = array('vote' => new Zend_Db_Expr('vote + 1'));
        $this->update($data, $this->getAdapter()->quoteInto('topicId = ?', $topicId));

        return (int)$res->vote + 1;
    }

    public function decrementVote($topicId)
    {
        $query = $this->select()
                ->from($this->_name,'vote')
                ->where('topicId = ?',$topicId);
        $res = $this->fetchRow($query);

        $data = array('vote' => new Zend_Db_Expr('vote - 1'));
        $this->update($data, $this->getAdapter()->quoteInto('topicId = ?', $topicId));

        return (int)$res->vote - 1;
    }

    public function editConfilct($messageBeforeSubmission, $topicId)
    {
        $row = $this->getTopic($topicId);
        if($row['message'] == $messageBeforeSubmission)
        {
            return false;
        }
        else
        {
            return true;
        }
    }
    
    public function getMotifByTopic($topic_id)
    {
        $query = $this->select()
                      ->setIntegrityCheck(false)
                      ->from($this->_name, null)
                      ->join('close_motif', $this->_name.'.topicId = close_motif.topic_id', array('motif', 'user_id'))
                      ->join('user', 'close_motif.user_id = user.id', array('login'))
                      ->where('close_motif.topic_id = ?',$topic_id);

        $res = $this->fetchAll($query);

        return $res;
    }
    
    public function sortTopics($sort_type, $closed_flag = true, $tag_name = null)
    {
        $order = $this->_name.'.date DESC';
        switch($sort_type)
        {
            case 'votes': 
                $order = $this->_name.'.vote DESC';
                if($tag_name != null)
                    $res = $this->getTopicsByTagName ($tag_name, $closed_flag, 50, $order);
                else
                    $res = $this->getAll($closed_flag, 50, $order);
                break;
            
            case 'responses': 
                $order = 'amount_messages DESC';
                if($tag_name != null)
                    $res = $this->getTopicsByTagName ($tag_name, $closed_flag, 50, $order);
                else
                    $res = $this->getAll($closed_flag, 50, $order);
                break;
            
            case 'unanswered':
                if($tag_name != null)
                    $res = $this->getUnansweredByTagName($tag_name, $closed_flag, 50, $order);
                else
                    $res = $this->getUnanswered($closed_flag, 50, $order);
                break;
        }
        
        return $res;
    }
    
    public function getUnanswered($closed_flag, $count = 50, $order = 'Topic.date DESC')
    {
        $orderby = $order;
        $query = $this->select();
        $query->setIntegrityCheck(false)
              ->from($this->_name, array(
                  'count(Messages.topicId) as amount_messages',
                  'topicId',
                  'title',
                  'userId',
                  'date',
                  'vote',
                  'status'
                  ))
              ->joinLeft(array('Messages'), 'Topic.topicId=Messages.topicId', array('messages_topicId' => 'topicId'))
              ->where('Messages.topicId IS NULL')
              ->group($this->_name.'.topicId')
              ->order($orderby)
              ->limit($count);
        
        if(!$closed_flag)
            $query->where('status != "closed"');

        $res = $this->fetchAll($query);
        return $res;
    }
    
    public function getUnansweredByTagName($tag_name, $closed_flag, $count = 50, $order = 'Topic.date DESC')
    {
        $orderby = $order;
        $query = $this->select();
        $query->setIntegrityCheck(false)
              ->from($this->_name, array(
                  'count(Messages.topicId) as amount_messages',
                  'topicId',
                  'title',
                  'userId',
                  'date',
                  'vote',
                  'status'
                  ))
              ->joinLeft('Messages', 'Topic.topicId=Messages.topicId', array('messages_topicId' => 'topicId'))
              ->join('TopicTag', 'Topic.topicId = TopicTag.topicId',array(
                                'topicTag_topicId' => 'topicId',
                                'topicTag_tagId' => 'tagId'
                                ))
              ->join('Tags', 'TopicTag.tagId = Tags.tagId',array(
                                'tag_tagId' => 'tagId'
                                ))
              ->where('Tags.name = ?', $tag_name)
              ->where('Messages.topicId IS NULL')
              ->group($this->_name.'.topicId')
              ->order($orderby)
              ->limit($count);
        
        if(!$closed_flag)
            $query->where('status != "closed"');

        $res = $this->fetchAll($query);
        return $res;
    }
}
?>
