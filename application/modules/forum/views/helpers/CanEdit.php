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
class Zend_View_Helper_CanEdit extends Zend_View_Helper_Abstract
{    
    public function canEdit($postAuthorId)
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            if($auth->getIdentity()->id == $postAuthorId)
                return true;
        }
        return false;
    }
}
?>
