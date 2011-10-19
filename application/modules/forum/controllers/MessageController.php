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
        $commentForm = new Forum_Form_UserPostMessage();
        $this->view->commentForm = $commentForm;

        if ($this->_request->isXmlHttpRequest()) {
            echo $commentForm;
        }

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if ($commentForm->isValid($formData)) {
                $content = $commentForm->getValue('content');
                $messageId = $this->_getParam('message');
                $topicId = $this->_getParam('topic');
                $comment = new Forum_Model_Comment();
                $commentMessage = new Forum_Model_CommentMessage();
                $commentId = $comment->addComment('1', $content);
                $commentMessage->addRow($commentId, $messageId);
                $this->_redirect('/topic/show/topic/' . $topicId);
            }
        }
    }

}

