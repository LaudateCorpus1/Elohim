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
                    echo $tag->getTagName($tagId)."/add";
            }
            else
            {
                $favoriteTag->deleteRow($identity->id, $tagId);
                if($this->_request->isXmlHttpRequest())
                    echo $tag->getTagName($tagId)."/remove";
                else
                    $this->view->favorited = true;
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
                    $favoriteTag->deleteRow($identity->id, $tagId);
        }
    }

    public function autocompleteAction()
    {
        $t = new Forum_Model_Tag();
        $res = $t->search($this->_getParam('term'));
        $this->_helper->json(array_values($res));
    }
    
    public function retagAction()
    {
        $id = $this->_getParam('topic');
        $topic = new Forum_Model_Topic();
        $tags = $topic->getTagsFromTopic($id)->toArray();
        $aOld_tag_name = array();
        $tags_string = "";
        foreach ($tags as $tag)
        {
            $aOld_tag_name[] = $tag['name'];
            $tags_string .= $tag['name']. " ";
        }
        $tags_string = substr($tags_string, 0, -1);
        $form = new Forum_Form_UserRetagTopic();
        $form->populate(array('tagsValues' => $tags_string));
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) 
        {
            $formData = $this->getRequest()->getPost();
            if($form->isValid($formData)) 
            {
                $tag_model = new Forum_Model_Tag();
                $aTags = array();
                $new_tags = $form->getValue('tagsValues');
                $aTags = explode(" ", $new_tags);
                
                $aDiff_tags_old = array_diff($aOld_tag_name, $aTags);
                $aDiff_tags_new = array_diff($aTags, $aOld_tag_name);
                
                $topic_tag_model = new Forum_Model_TopicTag();
                
                foreach ($aDiff_tags_old as $tag) 
                {
                    if (($tag_id = $tag_model->doesExist($tag)) !== false)
                    {
                            $topic_tag_model->deleteRow ($id, $tag_id);
                            $tag_model->decrementTag($tag);
                    }
                }
                
                foreach ($aDiff_tags_new as $tag) 
                {
                    if (($tag_model->doesExist($tag)) !== false) 
                    {
                        $tag_id = $tag_model->incrementTag($tag);
                    } 
                    else 
                    {
                        $tag_id = $tag_model->addTag($tag, '1');
                    }
                    $topic_tag_model->addRow($id, $tag_id);
                }
                
                $this->_redirect('/forum/topic/show/topic/' . $id);
            }
        }
    }
}



