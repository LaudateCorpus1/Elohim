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
            $where = $this->getAdapter()->quoteInto('TopicId = ?', $id);
            
            $query = $this->select();
            $query->setIntegrityCheck(false)
              ->from($this->_name, array(
                  'topicId',
                  'title',
                  'message',
                  'userId',
                  'date',
                  'vote',
                  'status',
                  'close_votes',
                  'reopen_votes',
                  'lastEditDate'
                  ))
              ->join('user', 'Topic.userId=user.id', array('login', 'avatar'))
              ->where($where);
            
            $row = $this->fetchRow($query);
            if (!$row)
            {
                    throw new Exception("Could not find row $id");
            }
            return $row;
    }
    
    public function getAll($closed_flag = true, $count = 50, $order = 'Topic.date DESC', $where = null)
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
                  'status',
                  'lastActivity'
                  ))
              ->joinLeft(array('Messages'), 'Topic.topicId=Messages.topicId', null)
              ->join('user', 'Topic.userId = user.id', 'login')
              ->group($this->_name.'.topicId')
              ->order($orderby)
              ->order('Topic.date DESC')
              ->limit($count);
        
        if(!$closed_flag)
            $query->where($this->_name.'.status != "closed"');
        
        if($where != null)
            $query->where($where);

        $res = $this->fetchAll($query);
        return $res;
    }
    
    public function getAuthor($id)
    {
        $query = $this->select()
                ->from($this->_name, 'userId')
                ->where($this->getAdapter()->quoteInto('topicId = ?',$id));
        $row = $this->fetchRow($query);
        
        if (!$row)
        {
            throw new Exception("Could not find row $id");
        }
        return $row;
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
            $this->update($data, $this->getAdapter()->quoteInto('topicId = ?', $id));
    }

    public function getMessagesFromTopic($topicId, $order = null)
    {
        $topicId = (int)$topicId;
        $query = $this->select();
        $query->setIntegrityCheck(false)
              ->from($this->_name,array(
                                'topic_topicId' => 'topicId'
                                ))
              ->join('Messages', 'Topic.topicId=Messages.topicId',array(
                                'messageId',
                                'messages_userId' => 'userId',
                                'messages_topicId' => 'topicId',
                                'content',
                                'messages_date' => 'date',
                                'messages_vote' => 'vote',
                                'validation',
                                'lastEditDate',
                                'userId'
                                ))
              ->join('user', 'Messages.userId = user.id', array('login', 'avatar')) 
              ->where($this->getAdapter()->quoteInto('Topic.topicId = ?',$topicId));
        
        if($order != null)
            $query->order($order);
        
        $query->order('Messages.validation DESC');
        $query->order('Messages.date ASC');
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
              ->where($this->getAdapter()->quoteInto('Topic.topicId = ?',$topicId));

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
                                'status',
                                'lastActivity'
                                ))
              ->joinLeft('Messages', 'Topic.topicId=Messages.topicId', null)
              ->join('TopicTag', 'Topic.topicId = TopicTag.topicId',array(
                                'topicTag_topicId' => 'topicId',
                                'topicTag_tagId' => 'tagId'
                                ))
              ->join('Tags', 'TopicTag.tagId = Tags.tagId',array(
                                'tag_tagId' => 'tagId'
                                ))
              ->join('user', 'Topic.userId = user.id', 'login')
              ->where('Tags.name = ?',$name)
              ->group($this->_name.'.topicId')
              ->order($orderby)
              ->order('Topic.date DESC')
              ->limit($count);
        
        if(!$closed_flag)
            $query->where($this->_name.'.status != "closed"');

        $res = $this->fetchAll($query);
        return $res;
    }

    public function incrementVote($topicId, $author_id)
    {        
        $this->getAdapter()->beginTransaction();
        
        $data = array('vote' => new Zend_Db_Expr('vote + 1'));
        $this->update($data, $this->getAdapter()->quoteInto('topicId = ?', $topicId));
   
        $query = $this->select()
                ->from($this->_name, array('vote', 'userId'))
                ->where($this->getAdapter()->quoteInto('topicId = ?', $topicId));
        $res = $this->fetchRow($query);
        
        if(!$res)
            throw new Exception("Could not find row $id");
        
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

    public function decrementVote($topicId, $author_id)
    {
        $this->getAdapter()->beginTransaction();
        
        $data = array('vote' => new Zend_Db_Expr('vote - 1'));
        $this->update($data, $this->getAdapter()->quoteInto('topicId = ?', $topicId));
        
        $query = $this->select()
                ->from($this->_name, array('vote', 'userId'))
                ->where($this->getAdapter()->quoteInto('topicId = ?',$topicId));
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
                      ->where($this->getAdapter()->quoteInto('close_motif.topic_id = ?',$topic_id));

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
                
            case 'activity':
                $order = 'lastActivity DESC';
                if($tag_name != null)
                    $res = $this->getTopicsByTagName ($tag_name, $closed_flag, 50, $order);
                else
                    $res = $this->getAll($closed_flag, 50, $order);
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
              ->join('user', 'Topic.userId = user.id', 'login')
              ->where('Messages.topicId IS NULL')
              ->group($this->_name.'.topicId')
              ->order($orderby)
              ->limit($count);
        
        if(!$closed_flag)
            $query->where($this->_name.'.status != "closed"');

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
              ->join('user', 'Topic.userId = user.id', 'login')
              ->where($this->getAdapter()->quoteInto('Tags.name = ?', $tag_name))
              ->where('Messages.topicId IS NULL')
              ->group($this->_name.'.topicId')
              ->order($orderby)
              ->limit($count);
        
        if(!$closed_flag)
            $query->where($this->_name.'.status != "closed"');

        $res = $this->fetchAll($query);
        return $res;
    }
    
    public function isClosed($topic_id)
    {
        $query = $this->select()
                ->from($this->_name, 'status')
                ->where($this->getAdapter()->quoteInto('topicId = ?',$topic_id));
        $row = $this->fetchRow($query);
        if (!$row)
            throw new Exception("Could not find row $topic_id");
        if($row->status == 'closed')
            return true;
        else
            return false;
    }
    
    public function incrementView($topicId)
    {
        $data = array('views' => new Zend_Db_Expr('views + 1'));
        $this->update($data, $this->getAdapter()->quoteInto('topicId = ?', $topicId));
    }
    
    public function getCommentsFromTopic($topicId)
    {
        $query = $this->select();
        $query->setIntegrityCheck(false)
              ->from($this->_name, 'topicId')
              ->join('commentTopic', 'Topic.topicId = commentTopic.topicId', null)
              ->join('Comments', 'commentTopic.commentId = Comments.commentId',array(
                                'commentId',
                                'userId',
                                'content',
                                'date'
                                ))
              ->join('user', 'Comments.userId = user.id', array('login'))
              ->where('Topic.topicId = ?',$topicId);

        $res = $this->fetchAll($query);

        return $res;
    }
    
    public function search($term)
    {
        /*$filter = function($tag) use ($term)
        {
            $tags = explode(" ", $term);
            $finalterm = end($tags);
            if(stristr($tag, $finalterm))
                    return true;
            return false;
        };
        $t = array();
        $taglist = $this->fetchAll();
        foreach($taglist as $topic)
        {
            $t[] = $topic->title;
        }
        return array_filter($t,$filter);*/
        
        $where = $this->getAdapter()->quoteInto('title LIKE ?', '%'.$term.'%');
        
        return $this->getAll(true, 50, 'Topic.date DESC', $where);
    }
}
?>
