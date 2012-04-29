<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->setLayout('layout');
        if ($this->_request->isXmlHttpRequest()) 
        {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();    //disable layout for ajax
        }
    }

    public function indexAction()
    {
        $news = new Model_News();
        $order = 'date_posted DESC';
        $count = 50;
        $list = $this->view->news = $news->fetchAll(null, $order, $count);
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
    
    public function testAction()
    {
        
    }
}

