<?php

class Administration_IndexController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout->setLayout('admin_layout');
        
        if ($this->_request->isXmlHttpRequest()) {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();    //disable layout for ajax
        }
    }

    public function indexAction()
    {
        $storyCount = 0;
        $reminderCount = 0;
        
        $auth = Zend_Auth::getInstance();
        $this->view->username = $auth->getIdentity()->login;
        
        $session = new Zend_Session_Namespace('islamine');
        if(isset($session->remindersToPublishToDevices)) {
            $reminderCount = count($session->remindersToPublishToDevices);
        }
        
        if(isset($session->storiesToPublishToDevices)) {
            $storyCount = count($session->storiesToPublishToDevices);
        }
        
        $this->view->reminderCount = $reminderCount;
        $this->view->storyCount = $storyCount;
    }
    
    public function generatesitemapAction()
    {
        $sitemap = new Islamine_Sitemap();
        $sitemap->buildSitemap();
    }
}







