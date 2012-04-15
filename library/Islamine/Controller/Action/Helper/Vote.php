<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Islamine_Controller_Action_Helper_Vote extends Zend_Controller_Action_Helper_Abstract
{
    public function direct($type)
    {
        $data = array('type' => $type);
        $object = strtolower(substr($type, strpos($type, '_')+1));
        $objectId = $this->getRequest()->getParam($object);
        $field = $object.'Id';
        
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();
        
        if($object == 'message')
        {
            $objectModel = new Forum_Model_Message();
            $topicId = Zend_Layout::getMvcInstance()->getView()->topic = $this->getRequest()->getParam('topic');
            $data['topicId'] = $topicId;
        }
        else if($object == 'topic')
        {
            $objectModel = new Forum_Model_Topic();
            $topicId = Zend_Layout::getMvcInstance()->getView()->topic = $this->getRequest()->getParam('topic');
        }
        else if($object == 'document') {
            $field = 'libraryId';
            $objectModel = new Default_Model_Library();
        }
        
        $model_karma = new Forum_Model_Karma();
        $lastAction = $model_karma->getLastAction(array('fromUserId' => $identity->id, $field => $objectId));
        
        $error = false;
        if($lastAction == null) {
           $data['cancellation'] = false;
        }
        else
        {
            // Si l'action demandée est le contraire de la dernière action
            // et que la dernière n'était pas une annulation, c'est une annulation
            if($lastAction->type != $data['type'] && $lastAction->cancellation == false)
                $data['cancellation'] = true;
            elseif($lastAction->type != $data['type'] && $lastAction->cancellation == true)
                $data['cancellation'] = false;
            // Si l'action demandée est la meme que la dernière et que celle-ci était une annulation
            elseif($lastAction->type == $data['type'] && $lastAction->cancellation == true)
                $data['cancellation'] = false;
            // Si l'action demandée est la meme que la dernière et que celle-ci n'était pas une annulation... impossible, cheater !
            elseif($lastAction->type == $data['type'] && $lastAction->cancellation == false)
            {
                $error = true;
                 if($this->getRequest()->isXmlHttpRequest())
                    echo Zend_Json::encode(array('status' => 'error', 'message' => 'Vous avez déjà voté'));
                else
                    Zend_Layout::getMvcInstance()->getView()->message = 'Vous avez déjà voté';
            }
        }
        
        if(!$error)
        {
            if(strpos($type, 'UP') !== false)
                $res = $objectModel->incrementVote($objectId, $identity->id);
            else
                $res = $objectModel->decrementVote($objectId, $identity->id);
            
            if($res === false)
            {
                if ($this->getRequest()->isXmlHttpRequest())
                    echo Zend_Json::encode(array('status' => 'error', 'message' => 'Vous ne pouvez pas voter pour vous'));
                else
                    Zend_Layout::getMvcInstance()->getView()->message = 'Vous ne pouvez pas voter pour vous';
            }
            else
            {
                if($data['cancellation']) {
                    // Il faut annuler l'ancien vote
                    $this->cancelVote($type, $res, $identity);
                }
                else {
                    $this->doVote($type, $res, $identity);
                }
                
                $data['fromUserId'] = $identity->id;
                $data['toUserId'] = $res->userId;
                $data[$field] = $objectId;
                
                if($lastAction != null) {
                    $model_karma->updateRow(array('outdated' => true), array('id' => $lastAction->id)); 
                }
                $model_karma->addKarmaAction($data);
                
                if($object == 'message' || $object == 'topic')
                {
                    // Mise à jour de l'activité du topic
                    $model_topic = new Forum_Model_Topic();
                    $model_topic->updateTopic(array('lastActivity' => date('Y-m-d H:i:s', time())), $topicId);
                }
                
                if ($this->getRequest()->isXmlHttpRequest())
                    echo Zend_Json::encode(array('status' => 'ok', 'vote' => $res->vote, 'type' => $type, 'revote' => $data['cancellation']));
                else
                    Zend_Layout::getMvcInstance()->getView()->message = 'Merci d\'avoir voté';
            }
        }
    }
    
    private function cancelVote($type, $res, $identity)
    {
        $user_model = new Model_User();
        switch($type)
        {
            case 'UP_TOPIC':
                $karma_down = Zend_Registry::getInstance()->constants->vote_topic_down_reward;
                $user_model->setKarma(abs(intval($karma_down)), $res->userId);
                break;
            
            case 'DOWN_TOPIC':
                $karma_up = Zend_Registry::getInstance()->constants->vote_topic_up_reward;
                $user_model->setKarma('-'.$karma_up, $res->userId);
                break;
            
            case 'UP_MESSAGE':
                $karma_down_author = Zend_Registry::getInstance()->constants->vote_message_down_author_reward;
                $user_model->setKarma(abs(intval($karma_down_author)), $res->userId);
                $karma_down_voter = Zend_Registry::getInstance()->constants->vote_message_down_voter_reward;
                $user_model->setKarma(abs(intval($karma_down_voter)), $identity->id);
                break;
            
            case 'DOWN_MESSAGE':
                $karma = Zend_Registry::getInstance()->constants->vote_message_up_reward;
                $user_model->setKarma('-'.$karma, $res->userId);
                break;
            
            case 'UP_DOCUMENT':
                $karma_down = Zend_Registry::getInstance()->constants->vote_document_down_reward;
                $user_model->setKarma(abs(intval($karma_down)), $res->userId);
                break;
            
            case 'DOWN_DOCUMENT':
                $karma_up = Zend_Registry::getInstance()->constants->vote_document_up_reward;
                $user_model->setKarma('-'.$karma_up, $res->userId);
                break;
        }
    }
    
    private function doVote($type, $res, $identity)
    {
        $user_model = new Model_User();
        switch($type)
        {
            case 'UP_TOPIC':
                $karma_up = Zend_Registry::getInstance()->constants->vote_topic_up_reward;
                $user_model->setKarma($karma_up, $res->userId);
                break;
            
            case 'DOWN_TOPIC':
                $karma_up = Zend_Registry::getInstance()->constants->vote_topic_down_reward;
                $user_model->setKarma($karma_up, $res->userId);
                break;
            
            case 'UP_MESSAGE':
                $karma_up = Zend_Registry::getInstance()->constants->vote_message_up_reward;
                $user_model->setKarma($karma_up, $res->userId);
                break;
            
            case 'DOWN_MESSAGE':
                $karma_down_author = Zend_Registry::getInstance()->constants->vote_message_down_author_reward;
                $user_model->setKarma($karma_down_author, $res->userId);
                $karma_down_voter = Zend_Registry::getInstance()->constants->vote_message_down_voter_reward;
                $user_model->setKarma($karma_down_voter, $identity->id);
                break;
            
            case 'UP_DOCUMENT':
                $karma_up = Zend_Registry::getInstance()->constants->vote_document_up_reward;
                $user_model->setKarma($karma_up, $res->userId);
                break;
            
            case 'DOWN_DOCUMENT':
                $karma_up = Zend_Registry::getInstance()->constants->vote_document_down_reward;
                $user_model->setKarma($karma_up, $res->userId);
                break;
        }
    }
}


?>
