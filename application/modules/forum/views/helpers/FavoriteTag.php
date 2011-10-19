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
class Zend_View_Helper_FavoriteTag extends Zend_View_Helper_Abstract
{
    public function favoriteTag($tagId,$userId)
    {
        $tags = new Forum_Model_Tag();
        if($tags->alreadyFavorited($tagId, $userId))
        {
            //return "-";
            return "<a href=\"/forum/tag/favorite/tag/$tagId\" class=\"fav-$tagId\" title=\"Retirer des favoris\">-</a>";
        }
        else
        {
            //return "+";
            return "<a href=\"/forum/tag/favorite/tag/$tagId\" class=\"fav-$tagId\" title=\"Ajouter en favoris\">+</a>";
        }
    }
}
?>
