<?php

class Forum_ErrorController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->setLayout('forum_layout');
        
        if ($this->_request->isXmlHttpRequest()) {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();    //disable layout for ajax
        }
    }

    public function indexAction()
    {
        // action body
    }

    public function karmaAction()
    {
        if($this->_getParam('message') != null)
        {
            $message = $this->_getParam('message');
            if($this->_request->isXmlHttpRequest())
                echo Zend_Json::encode(array('status' => 'error', 'message' => $message));
            else
                $this->view->error = $message;
        }
        else if($this->_getParam('privilege') != null)
        {
            $privilege = $this->_getParam('privilege');
            if ($this->_request->isXmlHttpRequest())
            {
                echo Zend_Json::encode(array('status' => 'error', 'message' => $privilege->message));
            }
            else
                $this->view->error = $privilege->message;
        }
        
    }


}



