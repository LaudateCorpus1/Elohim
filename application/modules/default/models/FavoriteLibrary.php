<?php
/**
 * Description of FavoriteTags
 *
 * @author jeremie
 */
class Default_Model_FavoriteLibrary extends Zend_Db_Table_Abstract
{
    protected $_name = 'favoriteLibrary';

    protected $_referenceMap = array(
        'User' => array(
                'columns'       => array('libraryId'),
                'refTableClass' => 'Default_Model_Lirary',
                'refColumns'    => array('id')
        ),
        'Tag'   => array(
                'columns'       => array('userId'),
                'refTableClass' => 'Model_User',
                'refColumns'    => array('id')
        ));

   public function addRow($userId, $libraryId)
   {
       $data = array(
           'userId' => $userId,
           'libraryId' => $libraryId
       );

       return $this->insert($data);
   }

   public function deleteRow($userId, $libraryId)
   {
       $this->delete(array('userId = ?' => $userId, 'libraryId = ?' => $libraryId));
   }
}
?>
