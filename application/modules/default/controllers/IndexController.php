<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->setLayout('index_layout');
        if($this->getRequest()->getActionName() == 'index')
        {
            $layout = Zend_Layout::getMvcInstance();
            $layout->class = 'class="index"';
        }
        if ($this->_request->isXmlHttpRequest()) 
        {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();    //disable layout for ajax
        }
    }

    public function indexAction()
    {
        $this->view->isUserConnected = false;
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $this->view->isUserConnected = true;
        }
        
        $modelEvent = new Default_Model_Event();
        $this->view->eventsAmount = $modelEvent->getEventsAmountInCurrentMonth();
        $modelLibrary = new Default_Model_Library();
        $this->view->documentsAmount = $modelLibrary->getDocumentsAmount();
        $modelUser = new Model_User();
        $this->view->usersAmount = $modelUser->getUsersAmount();
    }
    
    public function updatenotifbarAction()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $modelNotification = new Model_Notification();
            if($this->_getParam('notificationId'))
            {
                $modelNotification->updateNotification(array('beenRead' => true), $this->_getParam('notificationId'));
                if($this->_request->isXmlHttpRequest())
                    echo Zend_Json::encode(array('status' => 'ok'));
                else
                    $this->view->message = 'Ok';
            }
            else
            {
                if($this->_request->isXmlHttpRequest())
                    echo Zend_Json::encode(array('status' => 'error', 'message' => 'La notification n\'est pas définie'));
                else
                    $this->view->message = 'La notification n\'est pas définie';
            }
        }
    }
    
    public function copyrightAction()
    {
        
    }
    
    public function faqAction()
    {
        
    }
    
    public function privilegesAction()
    {
        
    }
    
    public function contactAction()
    {
        $this->view->form = $form = new Default_Form_Contact();

        if ($this->getRequest()->isPost()) 
        {
            $formData = $this->getRequest()->getPost();
            if($form->isValid($formData)) 
            {
                $subject = $form->getValue('form_contact_subject');
                $body = $form->getValue('form_contact_email').'...'.$form->getValue('form_contact_content');
                $this->_helper->alertMail($subject, $body);
                $this->view->message = 'Votre message a bien été envoyé.';
            }
        }
    }
    
    public function autocompletetagAction()
    {
        $t = new Forum_Model_Tag();
        $res = $t->search($this->_getParam('term'));
        $this->_helper->json(array_values($res));
    }
    
}

