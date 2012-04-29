<?php
/**
 * Description of Comment
 *
 * @author jeremie
 */
class Default_Model_CommentLibrary extends Zend_Db_Table_Abstract
{
    protected $_name = 'commentLibrary';

    public function getComment($id)
    {
        $row = $this->fetchRow(array('id = ?' => $id));
        if (!$row)
        {
            throw new Exception("Commentaire introuvable : $id");
        }
        return $row->toArray();
    }

    public function addComment(array $data)
    {
        return $this->insert($data);
    }

    public function deleteComment($id)
    {
        $this->delete($this->getAdapter()->quoteInto('id = ?', $id));
    }

    public function updateComment(array $data, $id)
    {
        $this->update($data, $this->getAdapter()->quoteInto('id = ?', $id));
    }
}
?>
