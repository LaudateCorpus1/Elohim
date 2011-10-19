<?php

class Forum_IndexController extends Zend_Controller_Action {

    public function init() {
        $this->_helper->layout->setLayout('forum_layout');
        
        if ($this->_request->isXmlHttpRequest()) {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();    //disable layout for ajax
        }
    }

    public function indexAction() {
        $i = 0;
        $topics = new Forum_Model_Topic();
        $order = 'date DESC';
        $count = 50;
        $list = $this->view->topics = $topics->fetchAll(null, $order, $count);
        foreach ($list as $topic) {
            $this->view->$i = $topics->getTagsFromTopic($topic->topicId);
            $i++;
        }

        // si l'utilisateur est connecté
        $favTags = new Forum_Model_Tag();
        $this->view->favTags = $favTags->getFavoriteTags('1');

        //        if($favTags->alreadyFavorited($tagId, '1'))
        //        $this->view->favorite = $this->view->favoriteTag("remove");
    }

}

