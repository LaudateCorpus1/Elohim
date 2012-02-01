<?php

class Islamine_Controller_Plugin_FavoriteTagModule extends Zend_Controller_Plugin_Abstract {

    /**
     * preDispatch()
     *
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $html = '';
        $layout = Zend_Layout::getMvcInstance();
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $tag_model = new Forum_Model_Tag();
            $identity = $auth->getIdentity();
            $fav_tags = $tag_model->getFavoriteTags($identity->id);
            
            $html .= '<div id="favtags">
                            Sujets favoris
                            <ul id="favlist">';
            
            if(count($fav_tags) > 0)
            {
                foreach($fav_tags as $fav_tag)
                {
                    $html .='<li class="favorited-style">
                                <a href="/forum/tagged/'.$fav_tag->name.'" class="favorited-'.$fav_tag->tags_tagId.'">'.$fav_tag->name.'</a>
                                <a href="/forum/tag/removefavorited/'.$fav_tag->tags_tagId.'" class="close2">x</a>
                            </li>';
                }
                $html .= '</ul>
                    </div>';
            }
        }
            
        $layout->module_favtags = $html;

    }

}
