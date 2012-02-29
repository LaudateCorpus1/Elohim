<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Islamine_Controller_Action_Helper_UpdateTags extends Zend_Controller_Action_Helper_Abstract
{
    public function direct($object_id, $new_tags, $targeted_object = 'topic')
    {
        $new_tags = strtolower($new_tags);
        $auth = Zend_Auth::getInstance();
        if($targeted_object == 'topic')
        {
            $modelObject = new Forum_Model_Topic();
            $tags = $modelObject->getTagsFromTopic($object_id)->toArray();
            $modelObjectTag = new Forum_Model_TopicTag();
            $amountColumn = 'amount';
        }
        else if($targeted_object == 'library')
        {
            $modelObject = new Default_Model_Library();
            $tags = $modelObject->getTags($object_id)->toArray();
            $modelObjectTag = new Default_Model_LibraryTag();
            $amountColumn = 'libraryAmount';
        }
        
        $aOld_tag_name = array();
        foreach ($tags as $tag)
        {
            $aOld_tag_name[] = $tag['name'];
        }
        $tag_model = new Forum_Model_Tag();
        $aTags = array();
        $aTags = explode(" ", $new_tags);

        $aDiff_tags_old = array_diff($aOld_tag_name, $aTags);
        $aDiff_tags_new = array_diff($aTags, $aOld_tag_name);

        $tag_model->getAdapter()->beginTransaction();
                
        foreach ($aDiff_tags_old as $tag) 
        {
            if (($tag_id = $tag_model->doesExist($tag)) !== false)
            {
                $modelObjectTag->deleteRow ($object_id, $tag_id);
                $tag_model->decrementTag($tag, $amountColumn);
            }
        }

        $error = false;
        foreach ($aDiff_tags_new as $tag) 
        {
            if (($tag_model->doesExist($tag)) !== false) 
            {
                $tag_id = $tag_model->incrementTag($tag, $amountColumn);
                $modelObjectTag->addRow($object_id, $tag_id);
            } 
            else 
            {
                $createTagsKarma = intval(Zend_Registry::getInstance()->constants->create_tags_karma);
                if(intval($auth->getIdentity()->karma) < intval($createTagsKarma))
                {
                    $error = true;
                }
                else
                {
                    $tag_id = $tag_model->addTag($tag, '1', $amountColumn);
                    $modelObjectTag->addRow($object_id, $tag_id);
                }

            }
        }
        if(!$error)
        {
            $tag_model->getAdapter()->commit();
            return true;
        }
        else
        {
            $tag_model->getAdapter()->rollBack();
            return false;
        }
    }
}


?>
