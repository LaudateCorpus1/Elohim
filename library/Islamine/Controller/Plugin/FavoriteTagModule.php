<?php

class Islamine_Controller_Plugin_FavoriteTagModule extends Zend_Controller_Plugin_Abstract {

    /**
     * preDispatch()
     *
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        if (!$request->isXmlHttpRequest()) 
        {
            $module = $this->getRequest()->getModuleName();
            switch ($module)
            {
                case 'default': $mod = 'doc';
                    break;
                case 'forum': $mod = 'forum';
                    break;
                default: $mod = 'doc';
                    break;
            }
            $html = '';
            $layout = Zend_Layout::getMvcInstance();
            $auth = Zend_Auth::getInstance();
            if($auth->hasIdentity())
            {
                $fav_tags = Zend_Registry::get('user')->favtags;

                $html .= '<div id="favtags">
                                Sujets favoris
                                <ul id="favlist">';

                if(count($fav_tags) > 0)
                {
                    foreach($fav_tags as $fav_tag)
                    {
                        $html .='<li class="favorited-style">
                                    <a href="/'.$mod.'/tagged/'.$fav_tag['name'].'" class="favorited-'.$fav_tag['tagId'].'">'.$fav_tag['name'].'</a>
                                    <a href="/forum/tag/removefavorited/'.$fav_tag['tagId'].'" class="close2">x</a>
                                </li>';
                    }
                    $html .= '</ul>
                        </div>';
                }
            }

            $layout->module_favtags = $html;
        }
    }

}
