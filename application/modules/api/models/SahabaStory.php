<?php

/**
 * Description of TopicTag
 *
 * @author jeremie
 */
class Api_Model_SahabaStory extends Zend_Db_Table_Abstract
{
    protected $_name = 'sahaba_story_link';

    protected $_referenceMap = array(
        'sahaba_story' => array(
                'columns'       => array('story_id'),
                'refTableClass' => 'Api_Model_Story',
                'refColumns'    => array('story_id')
        ),
        'sahaba'   => array(
                'columns'       => array('sahaba_id'),
                'refTableClass' => 'Api_Model_Sahaba',
                'refColumns'    => array('sahaba_id')
        ));

   public function addRow($storyId, $sahabaId)
   {
       $data = array(
           'story_id' => $storyId,
           'sahaba_id'   => $sahabaId
       );
       $this->insert($data);
   }
   
   public function deleteRow($storyId, $sahabaId)
   {
       $where = array();
       $where[] = $this->getAdapter()->quoteInto('story_id = ?', $storyId);
       $where[] = $this->getAdapter()->quoteInto('sahaba_id = ?', $sahabaId);
       $this->delete($where);
   }
   
    public function addMultipleTags($storyId, array $sahabasId)
    {
        if(count($sahabasId) != 0)
        {
            $data = array(
                   'sahaba_id' => $storyId
                );
            foreach ($sahabasId as $sahabaId)
            {
                $data['sahaba_id'] = $sahabaId;
                $this->insert($data);
            }
        }
    }
   
    public function updateRow(array $data, $storyId, $sahabaId)
    {
        $where = array();
        $where[] = $this->getAdapter()->quoteInto('story_id = ?', $storyId);
        $where[] = $this->getAdapter()->quoteInto('sahaba_id = ?', $sahabaId);
        $this->update($data, $where);
    }
    
    public function deleteByStoryId($storyId) 
    {
        $where = $this->getAdapter()->quoteInto('story_id = ?', $storyId);
        $this->delete($where);
    }
}
?>
