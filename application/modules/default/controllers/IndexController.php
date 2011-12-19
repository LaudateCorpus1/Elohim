<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
    }

    public function indexAction()
    {
        $news = new Model_News();
        $order = 'date_posted DESC';
        $count = 50;
        $list = $this->view->news = $news->fetchAll(null, $order, $count);
    }


}

