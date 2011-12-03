<?php

class Forum_MessageController extends Zend_Controller_Action {

    public function init() {
        $this->_helper->layout->setLayout('forum_layout');
        
        if ($this->_request->isXmlHttpRequest()) {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();    //disable layout for ajax
        }
    }

    public function indexAction() {
        // action body
    }

    public function decrementvoteAction() {
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();
        $decrementMessage = new Forum_Model_Message();
        $messageId = $this->_getParam('message');
        $this->view->topic = $this->_getParam('topic');
        $model_vote = new Forum_Model_Vote();
        
        if($model_vote->alreadyVoted($identity->id, $messageId, 'DOWN_MESSAGE'))
        {
            if($this->_request->isXmlHttpRequest())
                echo Zend_Json::encode(array('status' => 'error', 'error_message' => 'Vous avez déjà voté'));
            else
                $this->view->message = 'Vous avez déjà voté';
        }
        else
        {
            $res = $decrementMessage->decrementVote($messageId, $identity->id);
            if($res === false)
            {
                if ($this->_request->isXmlHttpRequest())
                    echo Zend_Json::encode(array('status' => 'error', 'error_message' => 'Vous ne pouvez pas voter pour vous'));
                else
                    $this->view->message = 'Vous ne pouvez pas voter pour vous';
            }
            else
            {
                $user_model = new Model_User();

                $revote = false;
                if($model_vote->alreadyVoted($identity->id, $messageId, 'UP_MESSAGE')) 
                {
                    $revote = true;
                    // Il faut annuler l'ancien vote sur ce message
                    $model_vote->deleteVote($identity->id, $messageId, 'MESSAGE');
                    $karma = Zend_Registry::getInstance()->constants->vote_message_up_reward;
                    $user_model->setKarma('-'.$karma, $res->userId);
                }
                else 
                {
                    $karma_down_author = Zend_Registry::getInstance()->constants->vote_message_down_author_reward;
                    $user_model->setKarma($karma_down_author, $res->userId);
                    $karma_down_voter = Zend_Registry::getInstance()->constants->vote_message_down_voter_reward;
                    $user_model->setKarma($karma_down_voter, $identity->id);
                    $model_vote->addVote($identity->id, $messageId, 'DOWN_MESSAGE');
                }

                if ($this->_request->isXmlHttpRequest())
                    echo Zend_Json::encode(array('status' => 'ok', 'vote' => $res->vote, 'type' => 'DOWN_MESSAGE', 'revote' => $revote));
                else
                    $this->view->message = 'Merci d\'avoir voté';
            }
        }
    }

    public function incrementvoteAction() {
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();
        $incrementMessage = new Forum_Model_Message();
        $messageId = $this->_getParam('message');
        $this->view->topic = $this->_getParam('topic');
        $model_vote = new Forum_Model_Vote();
        
        if($model_vote->alreadyVoted($identity->id, $messageId, 'UP_MESSAGE'))
        {
            if($this->_request->isXmlHttpRequest())
                echo Zend_Json::encode(array('status' => 'error', 'error_message' => 'Vous avez déjà voté'));
            else
                $this->view->message = 'Vous avez déjà voté';
        }
        else
        {
            $res = $incrementMessage->incrementVote($messageId, $identity->id);
            if($res === false)
            {
                if ($this->_request->isXmlHttpRequest())
                    echo Zend_Json::encode(array('status' => 'error', 'error_message' => 'Vous ne pouvez pas voter pour vous'));
                else
                    $this->view->message = 'Vous ne pouvez pas voter pour vous';
            }
            else
            {
                $user_model = new Model_User();

                $revote = false;

                if($model_vote->alreadyVoted($identity->id, $messageId, 'DOWN_MESSAGE'))
                {
                    $revote = true;
                    // Il faut supprimer l'ancien vote sur ce message
                    $model_vote->deleteVote($identity->id, $messageId, 'MESSAGE');
                    $karma_down_author = Zend_Registry::getInstance()->constants->vote_message_down_author_reward;
                    $user_model->setKarma(abs(intval($karma_down_author)), $res->userId);
                    $karma_down_voter = Zend_Registry::getInstance()->constants->vote_message_down_voter_reward;
                    $user_model->setKarma(abs(intval($karma_down_voter)), $identity->id);
                }
                else 
                {
                    $karma_up = Zend_Registry::getInstance()->constants->vote_message_up_reward;
                    $user_model->setKarma($karma_up, $res->userId);
                    $model_vote->addVote($identity->id, $messageId, 'UP_MESSAGE');
                }

                if ($this->_request->isXmlHttpRequest())
                    echo Zend_Json::encode(array('status' => 'ok', 'vote' => $res->vote, 'type' => 'UP_MESSAGE', 'revote' => $revote));
                else
                    $this->view->message = 'Merci d\'avoir voté';
            }
        }
    }

    public function commentAction() {
        $commentForm = new Forum_Form_UserPostComment();
        
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if ($commentForm->isValid($formData)) {
                $this->_processCommentForm($formData);
            }
        }
        $this->view->commentForm = $commentForm;
    }

    protected function _processCommentForm($data)
    {
        $content = $data['form_comment_content'];
        $messageId = $this->_getParam('message');
        $comment = new Forum_Model_Comment();
        $commentMessage = new Forum_Model_CommentMessage();
        
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $identity = $auth->getIdentity();
            $commentId = $comment->addComment($identity->id, $content);
            $commentMessage->addRow($commentId, $messageId);
            
            if ($this->_request->isXmlHttpRequest()) {
                echo Zend_Json::encode(array('status' => 'ok', 'user' => $identity->login, 'date' => '...'));
            } else {
                $topicId = $this->_getParam('topic');
                $this->_redirect('/forum/topic/show/topic/' . $topicId);
            }
        }
    }
    
    public function validateAction()
    {
        // Vérifier que l'utilisateur a le droit (s'il appelle l'adresse directement dans la barre et que la réponse n'a pas déjà été validée)
        $messageId = $this->_getParam('message');
        
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();
        
        $model_message = new Forum_Model_Message();
        $model_message->updateMessage(array('validation' => true), $messageId);
        $author = $model_message->getAuthor($messageId);
        
        $user_model = new Model_User();
        $karma_up_author = Zend_Registry::getInstance()->constants->message_validation_author_reward;
        $karma_up_validator = Zend_Registry::getInstance()->constants->message_validation_validator_reward;
        $user_model->setKarma($karma_up_validator, $identity->id);
        $user_model->setKarma($karma_up_author, $author->userId);
        
        if ($this->_request->isXmlHttpRequest()) {
                echo Zend_Json::encode(array('status' => 'ok'));
        } else {
            $this->view->message = 'Le message a été validé';
        }
    }
    
    public function devalidateAction()
    {
        // Vérifier que l'utilisateur a le droit (s'il appelle l'adresse directement dans la barre)
        $messageId = $this->_getParam('message');
        $this->view->topic = $this->_getParam('topic');
        
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();
        
        $model_message = new Forum_Model_Message();
        $model_message->updateMessage(array('validation' => false), $messageId);
        $author = $model_message->getAuthor($messageId);
        
        $user_model = new Model_User();
        $karma_up_author = Zend_Registry::getInstance()->constants->message_validation_author_reward;
        $karma_up_validator = Zend_Registry::getInstance()->constants->message_validation_validator_reward;
        $user_model->setKarma('-'.$karma_up_validator, $identity->id);
        $user_model->setKarma('-'.$karma_up_author, $author->userId);
        
        if ($this->_request->isXmlHttpRequest()) {
                echo Zend_Json::encode(array('status' => 'ok'));
        } else {
            $this->view->message = 'La validation a été annulée';
        }
    }
}

