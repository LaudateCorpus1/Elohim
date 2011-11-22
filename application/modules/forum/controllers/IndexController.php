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
        
        $r = $this->_getParam('topics');
        if($r != null)
            $list = $this->view->topics = $r;
        else
        {
            $closed_flag = $this->_helper->hasAccess('forum_topic', 'close');
            $list = $this->view->topics = $topics->getAll($closed_flag);
        }
        
        foreach ($list as $topic) {
            $this->view->$i = $topics->getTagsFromTopic($topic->topicId);
            $i++;
        }
        
        // si l'utilisateur est connecté
        $auth = Zend_Auth::getInstance();
        $autho = 'false';
        if($auth->hasIdentity())
        {
            $autho = 'true';
            $identity = $auth->getIdentity();
            $this->view->identity = $identity->id;
            $favTags = new Forum_Model_Tag();
            $this->view->favTags = $favTags->getFavoriteTags('1');

        //        if($favTags->alreadyFavorited($tagId, '1'))
        //        $this->view->favorite = $this->view->favoriteTag("remove");
        }
        
        $this->view->headScript()->appendScript("var auth = $autho;");
    }
    
    public function sortAction()
    {
        $sort_type = $this->_getParam('t');
        $tag_name = $this->_getParam('name');
        
        if($sort_type == 'date')
        {
            
            if($tag_name != null)
                $this->_redirect ('/forum/topic/tag/name/'.$tag_name);
            else
                $this->_redirect ('/forum');
        }
        else
        {
            $model_topic = new Forum_Model_Topic();
            $closed_flag = $this->_helper->hasAccess('forum_topic', 'close');
            $topics_sorted = $model_topic->sortTopics($sort_type, $closed_flag, $tag_name);
            $this->_forward('index', 'index', 'forum', array('topics' => $topics_sorted));
        }
    }

}

