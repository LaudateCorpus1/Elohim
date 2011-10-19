<?php

class Islamine_Controller_Plugin_Sidebar extends Zend_Controller_Plugin_Abstract {

    /**
     * preDispatch()
     *
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $layout = Zend_Layout::getMvcInstance();

        $nav = array();
        $model_category = new Model_Category();
        $categories = $model_category->getAll();
        $model_article = new Model_Article();
        
        foreach($categories as $category)
        {
            $articles = $model_article->getByCategory($category->id);
            $nav[$category->name] = $articles;
        }

        $layout->sidebar = $nav;

    }

}
