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

    public function getTopicsByTagName($name)
    {
        $query = $this->select();
        $query->setIntegrityCheck(false)
              ->from($this->_name,array(
                                'topic_topicId' => 'topicId',
                                'userId',
                                'title',
                                'message',
                                'date',
                                'vote'
                                ))
              ->join('TopicTag', 'Topic.topicId = TopicTag.topicId',array(
                                'topicTag_topicId' => 'topicId',
                                'topicTag_tagId' => 'tagId'
                                ))
              ->join('Tags', 'TopicTag.tagId = Tags.tagId',array(
                                'tag_tagId' => 'tagId'
                                ))
              ->where('Tags.name = ?',$name);

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
        $this->update($data, array('topicId = ?' => $topicId));

        return (int)$res->vote + 1;
    }

    public function decrementVote($topicId)
    {
        $query = $this->select()
                ->from($this->_name,'vote')
                ->where('topicId = ?',$topicId);
        $res = $this->fetchRow($query);

        $data = array('vote' => new Zend_Db_Expr('vote - 1'));
        $this->update($data, array('topicId = ?' => $topicId));

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
}
?>
