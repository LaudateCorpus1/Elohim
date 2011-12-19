<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of WikiTopic
 *
 * @author jeremie
 */
class Forum_Model_WikiTopic extends Model_Topic
{
    protected $_name = 'History';

    public function getHistory($id)
    {
            $id = (int)$id;
            $row = $this->fetchRow(array('historyId = ?' => $id));
            if (!$row)
            {
                    throw new Exception("Could not find row $id");
            }
            return $row->toArray();
    }


    public function addHistory($topicId, $userId, $ipAddress, $content, $date)
    {
            $data = array(
                'topicId' => $topicId,
                'userId' => $userId,
                'ipAddress' => $ipAddress,
                'content' => $content,
                'date' => $date
            );
            $this->insert($data);
    }

    public function deleteHistory($id)
    {
        $this->delete(array('historyId = ?' => $id));
    }

    public function updateHistory(array $data, $id)
    {
            $this->update($data, array('historyId = ?' => $id));
    }
}
?>
