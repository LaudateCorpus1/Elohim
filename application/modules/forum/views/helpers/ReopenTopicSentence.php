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
class Zend_View_Helper_ReopenTopicSentence extends Zend_View_Helper_Abstract
{
    public function reopenTopicSentence($reopen_votes)
    {
        $remaining_votes = 6 - intval($reopen_votes);
        $sentence = 'Après votre vote, <strong>'.$remaining_votes.'</strong> ';
        ($remaining_votes > 1) ? $sentence .= 'votes supplémentaires seront nécessaires pour que le sujet soit réouvert.' : $sentence .= 'vote supplémentaire sera nécessaire pour que le sujet soit réouvert.';
    
        return $sentence;
    }
}
?>
