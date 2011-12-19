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
        //$error = $this->_getParam('error_type');
        if ($this->_request->isXmlHttpRequest())
            echo "0";
        else
            $this->view->error = "Pas assez de karma";
    }


}



