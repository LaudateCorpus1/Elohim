<?php

class Forum_TagController extends Zend_Controller_Action
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
        $tag = new Forum_Model_Tag();
        $order = 'amount DESC';
        $count = 50;
        $this->view->tags = $tag->fetchAll(null, $order, $count);
    }

    public function favoriteAction()
    {
        $tag = new Forum_Model_Tag();
        $tagId = $this->_getParam('tag');
        $favoriteTag = new Forum_Model_FavoriteTags();

        if(!$tag->alreadyFavorited($tagId, '1'))
        {
            $favoriteTag->addRow('1', $tagId);
            if($this->_request->isXmlHttpRequest())
                echo $tag->getTagName($tagId)."/add";
        }
        else
        {
            $favoriteTag->deleteRow('1', $tagId);
            if($this->_request->isXmlHttpRequest())
                echo $tag->getTagName($tagId)."/remove";
            else
                $this->view->favorited = true;
        }
    }

    public function removefavoritedajaxAction()
    {
        $tag = new Forum_Model_Tag();
        $tagId = $this->_getParam('tag');
        $favoriteTag = new Forum_Model_FavoriteTags();
        if($tag->alreadyFavorited($tagId, '1'))
                $favoriteTag->deleteRow('1', $tagId);
    }

    public function autocompleteAction()
    {
        $t = new Forum_Model_Tag();
        $res = $t->search($this->_getParam('term'));
        $this->_helper->json(array_values($res));
    }
}



