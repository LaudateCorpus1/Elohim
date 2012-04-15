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
class Zend_View_Helper_KarmaTypeConverter extends Zend_View_Helper_Abstract
{
    public function karmaTypeConverter($type)
    {
        $html = '';
        switch($type)
        {
            case 'UP_TOPIC':
                $value = Zend_Registry::getInstance()->constants->vote_topic_up_reward;
                $html = '<span class="positive">'.$value.'</span> vote positif';
                break;
            
            case 'DOWN_TOPIC':
                $value = Zend_Registry::getInstance()->constants->vote_topic_down_reward;
                $html = '<span class="negative">'.$value.'</span> vote négatif';
                break;
            
            case 'UP_MESSAGE':
                $value = Zend_Registry::getInstance()->constants->vote_message_up_reward;
                $html = '<span class="positive">'.$value.'</span> vote positif';
                break;
            
            case 'DOWN_MESSAGE':
                $value = Zend_Registry::getInstance()->constants->vote_message_down_author_reward;
                $html = '<span class="negative">'.$value.'</span> vote négatif';
                break;
            
            case 'UP_DOCUMENT':
                $value = Zend_Registry::getInstance()->constants->vote_document_up_reward;
                $html = '<span class="positive">'.$value.'</span> vote positif';
                break;
            
            case 'DOWN_DOCUMENT':
                $value = Zend_Registry::getInstance()->constants->vote_document_down_reward;
                $html = '<span class="negative">'.$value.'</span> vote négatif';
                break;
            
            case 'VALIDATE_MESSAGE':
                $value = Zend_Registry::getInstance()->constants->message_validation_author_reward;
                $html = '<span class="positive">'.$value.'</span> message validé';
                break;
        }
        return $html;
    }
}
?>
