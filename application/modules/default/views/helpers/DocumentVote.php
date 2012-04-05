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
class Zend_View_Helper_DocumentVote extends Zend_View_Helper_Abstract
{
    public function documentVote($documentId, $type)
    {
        $html = $this->buildHTML($type, $documentId);
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $model_karma = new Forum_Model_Karma();
            $identity = $auth->getIdentity();
            
            $lastAction = $model_karma->getLastAction(array('fromUserId' => $identity->id, 'libraryId' => $documentId));
            if($lastAction != null && $lastAction->type == $type && $lastAction->cancellation == false)
            {
                switch($type)
                {
                    case 'UP_DOCUMENT':
                        $html = '<a class="disabled" title="Vous avez déjà voté pour"><img src="/images/arrow_right_grey.png" /></a>';
                        break;
                    
                    case 'DOWN_DOCUMENT':
                        $html = '<a class="disabled" title="Vous avez déjà voté contre"><img src="/images/arrow_left_grey.png" /></a>';
                        break;
                }
            }
        }
        
        return $html;
    }
    
    private function buildHTML($type, $documentId)
    {
        $html = '';
        switch($type)
        {
            case 'UP_DOCUMENT':
                $html = '<a href="'.$this->view->url(array('document' => $documentId
                                                    ), 'incrementDocumentVote').'" class="increment" title="Voter pour"><img src="/images/arrow_right_orange.gif" /></a>';
                break;

            case 'DOWN_DOCUMENT':
                $html = '<a href="'.$this->view->url(array('document' => $documentId
                                                    ), 'decrementDocumentVote').'" class="decrement" title="Voter contre"><img src="/images/arrow_left_orange.gif" /></a>';
                break;
        }
        return $html;
    }
}
?>
