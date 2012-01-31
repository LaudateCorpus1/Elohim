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
        $this->view->title = 'Forum';
        $r = $this->_getParam('topics');
        if($r != null)
        {
            $sort ='récents';
            if($this->_getParam('t'))
            {
                switch($this->_getParam('t'))
                {
                    case 'votes': $sort = 'les mieux votés'; 
                        break;
                    case 'responses': $sort = 'les plus répondus'; 
                        break;
                    case 'activity': $sort = 'les plus actifs'; 
                        break;
                    case 'unanswered': $sort = 'sans réponse'; 
                        break;
                    case 'interesting': $sort = 'les plus intéressants'; 
                        break;
                }
            }
            $this->view->title = 'Sujets '.$sort;
            if($this->_getParam('name'))
                $this->view->title .= ' sur \''.$this->_getParam('name').'\'';
            
            
            $list = $r;
        }
        else
        {
            //$closed_flag = $this->_helper->hasAccess('forum_topic', 'close');
            $list = $topics->getAll();
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
            $favTags = new Forum_Model_Tag();
            $this->view->favTags = $favTags->getFavoriteTags($identity->id);

        //        if($favTags->alreadyFavorited($tagId, '1'))
        //        $this->view->favorite = $this->view->favoriteTag("remove");
        }
        
        $page = Islamine_Paginator::factory($list);
        $page->setPageRange(5);
        $page->setCurrentPageNumber($this->_getParam('page',1));
        $page->setItemCountPerPage(20);
        $this->view->topics = $page;
        
        $this->view->headScript()->appendScript("var auth = $autho;");
    }
    
    public function searchAction()
    {
        $model_topic = new Forum_Model_Topic();
        $res = $model_topic->search($this->_getParam('search_content'));
        //Zend_Debug::dump($res); exit;
        $this->_forward('index', 'index', 'forum', array('topics' => $res));
    }

}

