<?php

/**
 * Description of TopicTag
 *
 * @author jeremie
 */
class Default_Model_LibraryTag extends Zend_Db_Table_Abstract
{
    protected $_name = 'libraryTag';

    protected $_referenceMap = array(
        'library' => array(
                'columns'       => array('libraryId'),
                'refTableClass' => 'Default_Model_Library',
                'refColumns'    => array('id')
        ),
        'Tag'   => array(
                'columns'       => array('tagId'),
                'refTableClass' => 'Forum_Model_Tag',
                'refColumns'    => array('tagId')
        ));

   public function addRow($libraryId, $tagId)
   {
       $data = array(
           'libraryId' => $libraryId,
           'tagId'   => $tagId
       );
       $this->insert($data);
   }
   
   public function deleteRow($libraryId, $tagId)
   {
       $where = array();
       $where[] = $this->getAdapter()->quoteInto('libraryId = ?', $libraryId);
       $where[] = $this->getAdapter()->quoteInto('tagId = ?', $tagId);
       $this->delete($where);
   }
   
    public function addMultipleTags($libraryId, array $tags)
    {
        if(count($tags) != 0)
        {
            $data = array(
                   'libraryId' => $libraryId
                );
            foreach ($tags as $tag)
            {
                $data['tagId'] = $tag;
                $this->insert($data);
            }
        }
    }
    
    public function deleteMultipleTags($libraryId, array $tags)
    {
        if(count($tags) != 0)
        {
            $where = array();
            $where[] = $this->getAdapter()->quoteInto('librayId = ?', $libraryId);
            $where[] = $this->getAdapter()->quoteInto('tagId IN(?)', $tags);
            $this->delete($data, $where);
        }
    }
   
    public function updateRow(array $data, $libraryId, $tagId)
    {
        $where = array();
        $where[] = $this->getAdapter()->quoteInto('libraryId = ?', $libraryId);
        $where[] = $this->getAdapter()->quoteInto('tagId = ?', $tagId);
        $this->update($data, $where);
    }
    
    public function deleteByTopicId($topicId) 
    {
        $where = $this->getAdapter()->quoteInto('topicId = ?', $topicId);
        $this->delete($where);
    }
}
?>
