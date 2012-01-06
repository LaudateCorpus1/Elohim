<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Zend_View_Helper_FavoriteTag
 *
 * @author jeremie
 */
class Zend_View_Helper_TopicContainerClass extends Zend_View_Helper_Abstract
{
    public function topicContainerClass($topic, $topic_tags)
    {
        $class = 'class = "topic"';
        if($topic->status == 'closed')
                $class = 'class = "topic closed" title="Ce sujet est fermé"';
        else
        {
            $auth = Zend_Auth::getInstance();
            if($auth->hasIdentity())
            {
                $identity = $auth->getIdentity();
                //$model_topic = new Forum_Model_Topic();
                //$topic_tags = $model_topic->getTagsFromTopic($topic->topicId);
                foreach($topic_tags as $topic_tag)
                {
                    if(in_array(array('tagId' => $topic_tag->tag_tagId, 'name' => $topic_tag->name), Zend_Registry::get('user')->favtags))
                    {
                        $class = 'class = "topic interest" title="Ce sujet peut vous intéresser"';
                        return $class;
                    }
                }
            }
        }
        return $class;
    }
}
?>
