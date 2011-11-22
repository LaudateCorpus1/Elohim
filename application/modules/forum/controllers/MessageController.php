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
        $decrementMessage = new Forum_Model_Message();
        $messageId = $this->_getParam('message');
        $this->view->topic = $this->_getParam('topic');
        $vote = $decrementMessage->decrementVote($messageId);
        if ($this->_request->isXmlHttpRequest())
                echo $vote;
    }

    public function incrementvoteAction() {
        $incrementMessage = new Forum_Model_Message();
        $messageId = $this->_getParam('message');
        $this->view->topic = $this->_getParam('topic');
        $vote = $incrementMessage->incrementVote($messageId);
        if ($this->_request->isXmlHttpRequest())
                echo $vote;
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
}

