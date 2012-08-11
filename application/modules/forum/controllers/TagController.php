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
        $this->view->tags = $tag->getAll()->toArray();
    }

    /*public function favoriteAction()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $identity = $auth->getIdentity();
            $tag = new Forum_Model_Tag();
            $tagId = $this->_getParam('tag');
            $favoriteTag = new Forum_Model_FavoriteTags();

            if(!$tag->alreadyFavorited($tagId, $identity->id))
            {
                $favoriteTag->addRow($identity->id, $tagId);
                if($this->_request->isXmlHttpRequest())
                    echo Zend_Json::encode(array('status' => 'ok', 'tagname' => $tag->getTagName($tagId), 'tagid' => $tagId, 'action' => 'add'));
                else
                    $this->view->message = 'Le mot-clé a été mis en favoris';
            }
            else
            {
                $favoriteTag->deleteRow($identity->id, $tagId);
                if($this->_request->isXmlHttpRequest())
                    echo Zend_Json::encode(array('status' => 'ok', 'tagname' => $tag->getTagName($tagId), 'tagid' => $tagId, 'action' => 'remove'));
                else
                    $this->view->message = 'Le mot-clé a été retiré en favoris';
            }
        }
    }*/

    public function addfavoritedAction()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $identity = $auth->getIdentity();
            $tag = new Forum_Model_Tag();
            $tagId = $this->_getParam('tag');
            $favoriteTag = new Forum_Model_FavoriteTags();
            
            if($tag->alreadyFavorited($tagId, $identity->id))
            {
                if($this->_request->isXmlHttpRequest())
                    echo Zend_Json::encode(array('status' => 'error', 'message' => 'Ce mot-clé est déjà en favoris'));
                else
                    $this->view->message = 'Ce mot-clé est déjà en favoris';
            }
            else
            {
                $favoriteTag->addRow($identity->id, $tagId);
                if($this->_request->isXmlHttpRequest())
                    echo Zend_Json::encode(array('status' => 'ok', 'tagname' => $tag->getTagName($tagId), 'tagid' => $tagId, 'action' => 'add'));
                else
                    $this->view->message = 'Le mot-clé a été mis en favoris';
            }
        }
    }
    
    public function removefavoritedAction()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $identity = $auth->getIdentity();
            $tag = new Forum_Model_Tag();
            $tagId = $this->_getParam('tag');
            $favoriteTag = new Forum_Model_FavoriteTags();
            if($tag->alreadyFavorited($tagId, $identity->id))
            {
                $favoriteTag->deleteRow($identity->id, $tagId);
                if($this->_request->isXmlHttpRequest())
                    echo Zend_Json::encode(array('status' => 'ok', 'tagname' => $tag->getTagName($tagId), 'tagid' => $tagId, 'action' => 'remove'));
                else
                    $this->view->message = 'Le mot-clé a été retiré en favoris';
            }
            else
            {
                if($this->_request->isXmlHttpRequest())
                    echo Zend_Json::encode(array('status' => 'error', 'message' => 'Ce mot-clé n\'est pas en favoris'));
                else
                    $this->view->message = 'Ce mot-clé n\'est pas en favoris';
            }
        }
    }

    /*public function autocompleteAction()
    {
        $t = new Forum_Model_Tag();
        $res = $t->search($this->_getParam('term'));
        $this->_helper->json(array_values($res));
    }*/
    
    public function retagAction()
    {
        $id = $this->_getParam('topic');
        $form = new Forum_Form_UserRetagTopic();
        $topic = new Forum_Model_Topic();
        $tags = $topic->getTagsFromTopic($id)->toArray();
        $tags_string = "";
        foreach ($tags as $tag)
        {
            $tags_string .= $tag['name']. " ";
        }
        $tags_string = substr($tags_string, 0, -1);
        $form->populate(array('tagsValues' => $tags_string));

        if ($this->getRequest()->isPost()) 
        {
            $formData = $this->getRequest()->getPost();
            if($form->isValid($formData)) 
            {
                $new_tags = $form->getValue('tagsValues');
                if($this->_helper->updateTags($id, $new_tags))
                    $this->_redirect('/forum/sujet/' . $id);
                else
                    $this->_forward('karma', 'error', 'forum', array('message' => 'Vous n\'avez pas le privilège pour créer des mots-clés'));
            }
        }
        $this->view->form = $form;
    }
}



