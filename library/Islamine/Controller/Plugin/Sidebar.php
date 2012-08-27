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
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        if($module == 'default' && $controller != 'index')
        {
            $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');
            
            $modelNews = new Model_News();
            $news = $modelNews->getLastNews();
            $html = '<div class="last-news"><h4>Les dernières news</h4>';
            foreach($news as $new)
            {
                $html .= '<div class="news">
                              <a href="'.$view->url(array(
                                                ), 'news').'">'.$new->title.'</a>
                              <span class="date">'.$new->date_posted.'</span>
                          </div>';
            }
            $layout->moduleNews = $html.'</div>';
        }
        /*if($module == 'default' && $controller == 'news')
        {
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
        }*/

    }

}
