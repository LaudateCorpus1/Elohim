<?php

class Forum_MessageController extends Zend_Controller_Action {

    public function init() {
        $this->_helper->layout->setLayout('forum_layout');
        
        if ($this->_request->isXmlHttpRequest()) {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();    //disable layout for ajax
        }
    }
    
    public function preDispatch()
    {
        if($this->_request->getActionName() == 'incrementvote' || $this->_request->getActionName() == 'decrementvote')
        {
            $maxVotesCast = intval(Zend_Registry::getInstance()->constants->max_votes_cast_per_day);
            $auth = Zend_Auth::getInstance();
            $model_karma = new Forum_Model_Karma();
            $userVotesCast = $model_karma->getTodayVotesCastByUser($auth->getIdentity()->id);
            if($userVotesCast >= $maxVotesCast)
            {
                $message = 'Vous avez atteint le quota maximum de vote par jour';
                $this->_forward('karma', 'error', 'forum', array('message' => $message));
            }
        }
    }

    public function indexAction() {
        // action body
    }

    public function decrementvoteAction() {
        $data = array('type' => 'DOWN_MESSAGE');
        
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();
        $decrementMessage = new Forum_Model_Message();
        $messageId = $this->_getParam('message');
        $topicId = $this->view->topic = $this->_getParam('topic');
        $model_karma = new Forum_Model_Karma();
        
        $lastAction = $model_karma->getLastAction(array('fromUserId' => $identity->id, 'messageId' => $messageId));
        
        $error = false;
        if($lastAction == null)
        {
           $data['cancellation'] = false;
        }
        else
        {
            // Si l'action demandée est le contraire de la dernière action, c'est une annulation
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
                 if($this->_request->isXmlHttpRequest())
                    echo Zend_Json::encode(array('status' => 'error', 'message' => 'Vous avez déjà voté'));
                else
                    $this->view->message = 'Vous avez déjà voté';
            }
        }
        
        if(!$error)
        {
            $res = $decrementMessage->decrementVote($messageId, $identity->id);
            if($res === false)
            {
                if ($this->_request->isXmlHttpRequest())
                    echo Zend_Json::encode(array('status' => 'error', 'message' => 'Vous ne pouvez pas voter pour vous'));
                else
                    $this->view->message = 'Vous ne pouvez pas voter pour vous';
            }
            else
            {
                $user_model = new Model_User();
                if($data['cancellation'])
                {
                    // Il faut annuler l'ancien vote sur ce message
                    $karma = Zend_Registry::getInstance()->constants->vote_message_up_reward;
                    $user_model->setKarma('-'.$karma, $res->userId);
                }
                else
                {
                    $karma_down_author = Zend_Registry::getInstance()->constants->vote_message_down_author_reward;
                    $user_model->setKarma($karma_down_author, $res->userId);
                    $karma_down_voter = Zend_Registry::getInstance()->constants->vote_message_down_voter_reward;
                    $user_model->setKarma($karma_down_voter, $identity->id);
                }
                
                $data['fromUserId'] = $identity->id;
                $data['toUserId'] = $res->userId;
                $data['messageId'] = $messageId;
                
                $model_karma->addKarmaAction($data);
                
                // Mise à jour de l'activité du topic
                $model_topic = new Forum_Model_Topic();
                $model_topic->updateTopic(array('lastActivity' => date('Y-m-d H:i:s', time())), $topicId);
                
                if ($this->_request->isXmlHttpRequest())
                    echo Zend_Json::encode(array('status' => 'ok', 'vote' => $res->vote, 'type' => 'DOWN_MESSAGE', 'revote' => $data['cancellation']));
                else
                    $this->view->message = 'Merci d\'avoir voté';
            }
        }
    }

    public function incrementvoteAction() {
        $data = array('type' => 'UP_MESSAGE');
        
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();
        $incrementMessage = new Forum_Model_Message();
        $messageId = $this->_getParam('message');
        $topicId = $this->view->topic = $this->_getParam('topic');
        $model_karma = new Forum_Model_Karma();
        
        $lastAction = $model_karma->getLastAction(array('fromUserId' => $identity->id, 'messageId' => $messageId));
        
        $error = false;
        if($lastAction == null)
        {
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
                 if($this->_request->isXmlHttpRequest())
                    echo Zend_Json::encode(array('status' => 'error', 'message' => 'Vous avez déjà voté'));
                else
                    $this->view->message = 'Vous avez déjà voté';
            }
        }
        
        if(!$error)
        {
            $res = $incrementMessage->incrementVote($messageId, $identity->id);
            if($res === false)
            {
                if ($this->_request->isXmlHttpRequest())
                    echo Zend_Json::encode(array('status' => 'error', 'message' => 'Vous ne pouvez pas voter pour vous'));
                else
                    $this->view->message = 'Vous ne pouvez pas voter pour vous';
            }
            else
            {
                $user_model = new Model_User();
                if($data['cancellation'])
                {
                    // Il faut annuler l'ancien vote sur ce message
                    $karma_down_author = Zend_Registry::getInstance()->constants->vote_message_down_author_reward;
                    $user_model->setKarma(abs(intval($karma_down_author)), $res->userId);
                    $karma_down_voter = Zend_Registry::getInstance()->constants->vote_message_down_voter_reward;
                    $user_model->setKarma(abs(intval($karma_down_voter)), $identity->id);
                }
                else
                {
                    $karma_up = Zend_Registry::getInstance()->constants->vote_message_up_reward;
                    $user_model->setKarma($karma_up, $res->userId);
                }
                
                $data['fromUserId'] = $identity->id;
                $data['toUserId'] = $res->userId;
                $data['messageId'] = $messageId;
                
                $model_karma->addKarmaAction($data);
                
                // Mise à jour de l'activité du topic
                $model_topic = new Forum_Model_Topic();
                $model_topic->updateTopic(array('lastActivity' => date('Y-m-d H:i:s', time())), $topicId);
                
                if ($this->_request->isXmlHttpRequest())
                    echo Zend_Json::encode(array('status' => 'ok', 'vote' => $res->vote, 'type' => 'UP_MESSAGE', 'revote' => $data['cancellation']));
                else
                    $this->view->message = 'Merci d\'avoir voté';
            }
        }
    }

    public function commentAction() {
        
        $topicId = $this->_getParam('topic');
        $model_topic = new Forum_Model_Topic();
        $closed = $model_topic->isClosed($topicId);
        
        if(!$closed)
        {
            $commentForm = new Forum_Form_UserPostComment();

            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();

                if ($commentForm->isValid($formData)) {
                    $this->_processCommentForm($formData, $topicId);
                }
                else
                {
                    if($this->_request->isXmlHttpRequest())
                        echo Zend_Json::encode(array('status' => 'error', 'message' => 'Le commentaire n\'est pas valide'));
                }
            }
            $this->view->commentForm = $commentForm;
        }
        else
        {
            if($this->_request->isXmlHttpRequest())
                echo Zend_Json::encode(array('status' => 'error', 'message' => 'Ce sujet est fermé, vous ne pouvez pas y écrire de commentaire'));
            else
                $this->view->message = 'Ce sujet est fermé, vous ne pouvez pas y écire de commentaire';
        }
    }

    protected function _processCommentForm($data, $topicId)
    {
        $content = $data['form_comment_content'];
        $messageId = $this->_getParam('message');
        $comment = new Forum_Model_Comment();
        $model_topic = new Forum_Model_Topic();
        $commentMessage = new Forum_Model_CommentMessage();
        
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $identity = $auth->getIdentity();
            $commentDate = date('Y-m-d H:i:s', time());
            $commentId = $comment->addComment($identity->id, $content, $commentDate);
            $commentMessage->addRow($commentId, $messageId);
            $model_topic->updateTopic(array('lastActivity' => date('Y-m-d H:i:s', time())), $topicId);
            
            if ($this->_request->isXmlHttpRequest()) {
                echo Zend_Json::encode(array('status' => 'ok', 'user' => $identity->login, 'userId' => $identity->id, 'commentId' => $commentId, 'date' => $commentDate));
            } else {
                $this->_redirect('/forum/sujet/'.$topicId);
            }
        }
    }
    
    public function validateAction()
    {
        // Vérifier que l'utilisateur a le droit (s'il appelle l'adresse directement dans la barre et que la réponse n'a pas déjà été validée)
        $messageId = $this->_getParam('message');
        $this->view->topic = $topicId = $this->_getParam('topic');
        
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();
        
        $model_topic = new Forum_Model_Topic();
        $topic = $model_topic->getTopic($topicId);
        
        $model_message = new Forum_Model_Message();
        $message_author = $model_message->getAuthor($messageId);
        
        if(($topic->userId == $identity->id) && ($message_author->userId != $identity->id) && ($topic->status != 'resolved'))
        {
            $rows_affected = $model_message->updateMessage(array('validation' => true), $messageId, $topicId);
            if($rows_affected > 0)
            {
                $model_topic->updateTopic(array('status' => 'resolved'), $topicId);

                $user_model = new Model_User();
                $karma_up_author = Zend_Registry::getInstance()->constants->message_validation_author_reward;
                $karma_up_validator = Zend_Registry::getInstance()->constants->message_validation_validator_reward;
                $user_model->setKarma($karma_up_validator, $identity->id);
                $user_model->setKarma($karma_up_author, $message_author->userId);
                
                $model_karma = new Forum_Model_Karma();
                $data = array(
                    'type' => 'VALIDATE_MESSAGE',
                    'toUserId' => $message_author->userId,
                    'fromUserId' => $identity->id,
                    'messageId' => $messageId,
                    'topicId' => $topicId
                );
                $model_karma->addKarmaAction($data);

                if ($this->_request->isXmlHttpRequest()) {
                        echo Zend_Json::encode(array('status' => 'ok'));
                } else {
                    $this->view->message = 'Le message a été validé';
                }
            }
            else
            {
                if ($this->_request->isXmlHttpRequest()) {
                    echo Zend_Json::encode(array('status' => 'error', 'message' => 'Vous n\'avez pas le droit d\'effectuer cette action'));
                } else {
                    $this->view->message = 'Vous n\'avez pas le droit d\'effectuer cette action';
                }
            }
        }
        else 
        {
            if ($this->_request->isXmlHttpRequest()) {
                    echo Zend_Json::encode(array('status' => 'error', 'message' => 'Vous n\'avez pas le droit d\'effectuer cette action'));
            } else {
                $this->view->message = 'Vous n\'avez pas le droit d\'effectuer cette action';
            }
        }
    }
    
    public function devalidateAction()
    {
        // Vérifier que l'utilisateur a le droit (s'il appelle l'adresse directement dans la barre)
        $messageId = $this->_getParam('message');
        $this->view->topic = $topicId = $this->_getParam('topic');
        
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();
        
        $model_topic = new Forum_Model_Topic();
        $topic = $model_topic->getTopic($topicId);
        
        $model_message = new Forum_Model_Message();
        $message = $model_message->getMessage($messageId);
        
        if(($topic->userId == $identity->id) && ($message->userId != $identity->id) && ($topic->status == 'resolved') && ($message->validation == true))
        {
            $rows_affected = $model_message->updateMessage(array('validation' => false), $messageId, $topicId);
            if($rows_affected > 0)
            {
                $model_topic->updateTopic(array('status' => ''), $topicId);

                $user_model = new Model_User();
                $karma_up_author = Zend_Registry::getInstance()->constants->message_validation_author_reward;
                $karma_up_validator = Zend_Registry::getInstance()->constants->message_validation_validator_reward;
                $user_model->setKarma('-'.$karma_up_validator, $identity->id);
                $user_model->setKarma('-'.$karma_up_author, $message->userId);
                
                $model_karma = new Forum_Model_Karma();
                $data = array(
                    'type' => 'DEVALIDATE_MESSAGE',
                    'toUserId' => $message->userId,
                    'fromUserId' => $identity->id,
                    'messageId' => $messageId,
                    'topicId' => $topicId
                );
                $model_karma->addKarmaAction($data);

                if ($this->_request->isXmlHttpRequest()) {
                        echo Zend_Json::encode(array('status' => 'ok'));
                } else {
                    $this->view->message = 'La validation a été annulée';
                }
            }
            else
            {
                if ($this->_request->isXmlHttpRequest()) {
                    echo Zend_Json::encode(array('status' => 'error', 'message' => 'Vous n\'avez pas le droit d\'effectuer cette action'));
                } else {
                    $this->view->message = 'Vous n\'avez pas le droit d\'effectuer cette action';
                }
            }
        }
        else 
        {
            if ($this->_request->isXmlHttpRequest()) {
                    echo Zend_Json::encode(array('status' => 'error', 'message' => 'Vous n\'avez pas le droit d\'effectuer cette action'));
            } else {
                $this->view->message = 'Vous n\'avez pas le droit d\'effectuer cette action';
            }
        }
    }
    
    public function sortAction()
    {
        $sort_type = $this->_getParam('t');
        $topicId = $this->_getParam('topic');
            
        if($sort_type == 'date')
            $this->_redirect('/forum/sujet/'.$topicId);
        else
        {
            $model_message = new Forum_Model_Message();
            $messages_sorted = $model_message->sortMessages($topicId, $sort_type);
            $this->_forward('show', 'topic', 'forum', array('messages' => $messages_sorted));
        }
    }
    
    public function editAction()
    {
        $messageId = $this->_getParam('id');
        $model_message = new Forum_Model_Message();
        $message = $model_message->getMessage($messageId);
        $model_topic = new Forum_Model_Topic();
        $closed = $model_topic->isClosed($message->topicId);
        
        if(!$closed)
        {
            $this->view->headScript()->appendFile("/js/answerEditor.js");
            $messageForm = new Forum_Form_UserPostMessage();
            $messageForm->getElement('form_message_content')->setAttrib('class', 'edit_message');
            $messageForm->populate(array('form_message_content' => $message->content));
                    
            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();

                if ($messageForm->isValid($formData)) {
                    $auth = Zend_Auth::getInstance();
                    if($auth->hasIdentity())
                    {
                        $identity = $auth->getIdentity();
                        $content = $messageForm->getValue('form_message_content');

                        $date = date('Y-m-d H:i:s', time());
                        $data = array('content' => $content,
                                      'lastEditDate' => $date,
                                      'lastActivity' => $date,
                            );
                        $model_message->updateMessage($data, $messageId);
                        $model_topic->updateTopic(array('lastActivity' => $date), $message->topicId);
                        
                        $this->_redirect('/forum/sujet/'.$message->topicId.'#'.$messageId);
                    }
                }
            }
            $this->view->messageForm = $messageForm;
        }
        else
        {
            $this->view->message = 'Ce sujet est fermé, vous ne pouvez pas y écire de commentaire';
        }
    }
}

