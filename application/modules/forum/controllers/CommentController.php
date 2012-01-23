<?php

class Forum_CommentController extends Zend_Controller_Action {

    public function init() {
        $this->_helper->layout->setLayout('forum_layout');
                
        if ($this->_request->isXmlHttpRequest()) {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();
        }
    }

    public function indexAction() {
    }

    public function editAction()
    {
        $topicId = $this->_getParam('topic');
        $model_topic = new Forum_Model_Topic();
        $closed = $model_topic->isClosed($topicId);
        
        if(!$closed)
        {
            $commentForm = new Forum_Form_UserPostComment();

            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();

                if ($commentForm->isValid($formData)) {
                    $this->_processEditComment($formData, $topicId);
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
    
    protected function _processEditComment($data, $topicId)
    {
        $content = $data['form_comment_content'];
        $commentId = $this->_getParam('message');
        $model_comment = new Forum_Model_Comment();
        $model_topic = new Forum_Model_Topic();
        
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $identity = $auth->getIdentity();
            $editDate = date('Y-m-d H:i:s', time());
            $model_comment->updateComment(array('content' => $content, 'lastEditDate' => $editDate), $commentId);
            $model_topic->updateTopic(array('lastActivity' => date('Y-m-d H:i:s', time())), $topicId);
            
            if ($this->_request->isXmlHttpRequest()) {
                echo Zend_Json::encode(array('status' => 'ok', 'user' => $identity->login, 'date' => $editDate));
            } else {
                $this->_redirect('/forum/' . $topicId);
            }
        }
    }
}

