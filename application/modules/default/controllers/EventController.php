<?php

class EventController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout->disableLayout(); 
    }

    public function indexAction()
    {
        if($_SERVER['HTTP_REFERER'] != null)
            $this->view->backUrl = $_SERVER['HTTP_REFERER'];
        
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $this->view->userId = $auth->getIdentity()->id;
        }
        else
        {
            $this->view->userId = 0;
        }
    }
}

