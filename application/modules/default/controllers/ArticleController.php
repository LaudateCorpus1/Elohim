<?php

class ArticleController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        
    }

    public function getAction()
    {
        // action body
    }

    public function showAction()
    {
        $article_id = $this->_getParam('id');
        $model = new Model_Article();
        $this->view->article = $model->get($article_id);

        $this->view->comments = $model->getComments($article_id);

        $comment_form = new Default_Form_Comment();
        $this->view->comment_form = $comment_form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if ($comment_form->isValid($formData)) {
                $content = $comment_form->getValue('content');
                $author = $comment_form->getValue('pseudo');
                $comment = new Model_Comment();
                $comment->insertComment($author, $content, $article_id);
                $this->_redirect('/article/index/id/' . $article_id);
            }
        }
    }


}





