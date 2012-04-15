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
class Zend_View_Helper_FavoriteDocument extends Zend_View_Helper_Abstract
{
    public function favoriteDocument($documentId)
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $userId = $auth->getIdentity()->id;
            $modelLibrary = new Default_Model_Library();
            if($modelLibrary->alreadyFavorited($documentId, $userId)) {
                return '<a href="/doc/'.$documentId.'/removefavorite/" class="favdoc-'.$documentId.'" title="Retirer des favoris"><img class="remove" src="/images/moins.png" alt="retirerfavoris"/></a>';
            }
            else {
                return '<a href="/doc/'.$documentId.'/addfavorite/" class="favdoc-'.$documentId.'" title="Ajouter en favoris"><img class="add" src="/images/plus2.png" alt="ajouterfavoris"/></a>';
            }
        }
        else {
            return '<a href="/doc/'.$documentId.'/addfavorite/" class="favdoc-'.$documentId.'" title="Ajouter en favoris"><img class="add" src="/images/plus2.png" alt="ajouterfavoris"/></a>';
        }
    }
}
?>
