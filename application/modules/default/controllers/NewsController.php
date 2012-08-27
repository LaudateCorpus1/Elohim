<?php

class NewsController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $model = new Model_News();
        $news = $model->getAll();
        
        $page = new Islamine_Paginator(new Zend_Paginator_Adapter_DbSelect($news));
        $page->setPageRange(5);
        $page->setCurrentPageNumber($this->_getParam('page',1));
        $page->setItemCountPerPage(20);
        $this->view->news = $page;
    }


}

