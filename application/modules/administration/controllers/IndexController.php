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
        $auth = Zend_Auth::getInstance();
        $this->view->username = $auth->getIdentity()->login;
    }
    
    public function generatesitemapAction()
    {
        $sitemap = new Islamine_Sitemap();
        $sitemap->buildSitemap();
    }
}







