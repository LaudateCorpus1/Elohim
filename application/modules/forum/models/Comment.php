<?php
/**
 * Description of Comment
 *
 * @author jeremie
 */
class Forum_Model_Comment extends Zend_Db_Table_Abstract
{
    protected $_name = 'Comments';

    public function getComment($id)
    {
            $id = (int)$id;
            $row = $this->fetchRow(array('commentId = ?' => $id));
            if (!$row)
            {
                    throw new Exception("Could not find row $id");
            }
            return $row->toArray();
    }

    public function addComment($userId,$content, $date = null)
    {
        $data = array(
            'userId' => $userId,
            'content' => $content,
            'date' => $date
        );
        return $this->insert($data);
    }

    public function deleteComment($id)
    {
        $this->delete(array('commentId = ?' => $id));
    }

    public function updateComment(array $data, $id)
    {
        $this->update($data, array('commentId = ?' => $id));
    }
}
?>
