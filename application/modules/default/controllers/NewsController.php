<?php

class NewsController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->setLayout('layout');
        
        if ($this->_request->isXmlHttpRequest()) {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout(); //disable layout for ajax
        }
    }

    public function indexAction()
    {
        $model = new Model_News();
        $news = $model->getAll();
        $canModify = false;
        $auth = Zend_Auth::getInstance();
        
        if($auth->hasIdentity())
        {
            $adminRole = Zend_Registry::getInstance()->constants->roles->admin;
            if($auth->getIdentity()->role == $adminRole
                    && $this->_helper->hasAccess('news', 'edit'))
            {
                $canModify = true;
            }
        }
        $this->view->canModify = $canModify;
        
        $page = new Islamine_Paginator(new Zend_Paginator_Adapter_DbSelect($news));
        $page->setPageRange(5);
        $page->setCurrentPageNumber($this->_getParam('page',1));
        $page->setItemCountPerPage(20);
        $this->view->news = $page;
    }
    
    public function addAction()
    {
        $form = new Default_Form_News();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $title = $form->getValue('form_news_title');
                $content = $form->getValue('form_news_content');
                
                $modelNews = new Model_News();
                $auth = Zend_Auth::getInstance();
                
                $modelNews->addNews($auth->getIdentity()->id, $title, $content);
                $this->_redirect('/news');
            }
        }
        $this->view->form = $form;
    }
    
    public function editAction()
    {
       $auth = Zend_Auth::getInstance();
       $id = $this->_getParam('id');
       $modelNews = new Model_News();
       $news = $modelNews->get($id);
       
       $form = new Default_Form_News();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {

                $title = $form->getValue('form_news_title');
                $content = $form->getValue('form_news_content');
                $date = date('Y-m-d H:i:s', time());
                $modelNews->updateNews(array(
                                                'title' => $title,
                                                'content' => $content,
                                                'last_updated' => $date
                                             ), $id);
                
                $this->_redirect('/news');
            }
        }
        
        $form->populate(array(
                        'form_news_title' => $news->title,
                        'form_news_content' => $news->content
                       ));
        $this->view->form = $form;
    }
    
    public function deleteAction()
    {
        $id = $this->_getParam('id');
        $modelNews = new Model_News();
        $modelNews->deleteNews($id);
        $this->_redirect('/news');
    }
}

