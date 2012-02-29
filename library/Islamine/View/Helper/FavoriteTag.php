<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Zend_View_Helper_FavoriteTag
 *
 * @author jeremie
 */
class Islamine_View_Helper_FavoriteTag extends Zend_View_Helper_Abstract
{
    public function favoriteTag($tagId, $tagName)
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $userId = $auth->getIdentity()->id;
            $tags = new Forum_Model_Tag();
            if(in_array(array('tagId' => $tagId, 'name' => $tagName), Zend_Registry::get('user')->favtags))
            {
                //return "-";
                return '<a href="/forum/tag/removefavorited/'.$tagId.'" class="fav-'.$tagId.'" title="Retirer des favoris"><img class="remove" src="/images/moins.png" alt="retirerfavoris"/></a>';
            }
            else
            {
                //return "+";
                return '<a href="/forum/tag/addfavorited/'.$tagId.'" class="fav-'.$tagId.'" title="Ajouter en favoris"><img class="add" src="/images/plus2.png" alt="ajouterfavoris"/></a>';
            }
        }
        else
        {
            return '<a href="/forum/tag/addfavorited/'.$tagId.'" class="fav-'.$tagId.'" title="Ajouter en favoris"><img class="add" src="/images/plus2.png" alt="ajouterfavoris"/></a>';
        }
    }
}
?>
