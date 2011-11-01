<?php
/**
 * Description of FavoriteTags
 *
 * @author jeremie
 */
class Forum_Model_FavoriteTags extends Zend_Db_Table_Abstract
{
    protected $_name = 'FavoritesTags';

    protected $_referenceMap = array(
        'User' => array(
                'columns'       => array('userId'),
                'refTableClass' => 'Forum_Model_Topic',
                'refColumns'    => array('userId')
        ),
        'Tag'   => array(
                'columns'       => array('tagId'),
                'refTableClass' => 'Forum_Model_Tag',
                'refColumns'    => array('tagId')
        ));

   public function addRow($userId,$tagId)
   {
       $data = array(
           'userId' => $userId,
           'tagId'   => $tagId
       );

       return $this->insert($data);
   }

   public function deleteRow($userId,$tagId)
   {
       $this->delete(array('userId = ?' =>$userId, 'tagId = ?' => $tagId));
   }
}
?>
