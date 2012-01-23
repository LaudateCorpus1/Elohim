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
class Zend_View_Helper_LinkEditComment extends Zend_View_Helper_Abstract
{
    public function linkEditComment($commentAuthorId, $commentId, $controller)
    {
        $html = '';
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $identity = $auth->getIdentity();
            if($identity->id == $commentAuthorId)
            {
                $html = ' - <a href="'.$this->view->url(array(
                                               'id' => $this->view->escape($commentId)
                                            ), 'editComment', true).'" class="edit-comment-'.$commentId.'">Editer</a>';
            }
            
        }
        
        return $html;
    }
}
?>
