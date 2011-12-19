<?php

class NewsController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $id = $this->_getParam('id');
        $model = new Model_News();
        $this->view->news = $model->get($id);
    }


}

