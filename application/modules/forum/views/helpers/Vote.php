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
class Zend_View_Helper_Vote extends Zend_View_Helper_Abstract
{
    public function vote($messageId, $type)
    {
        $html = $this->buildHTML($type, $messageId);
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            //$model_vote = new Forum_Model_Vote();
            $model_karma = new Forum_Model_Karma();
            $identity = $auth->getIdentity();
            strpos($type, 'TOPIC') !== false ? $field = 'topicId' : $field = 'messageId';
            
            $lastAction = $model_karma->getLastAction(array('fromUserId' => $identity->id, $field => $messageId));
            //if($model_vote->alreadyVoted($identity->id, $messageId, $type))
            if($lastAction != null && $lastAction->type == $type && $lastAction->cancellation == false)
            {
                switch($type)
                {
                    case 'UP_MESSAGE':
                        $html = '<a class="disabled" title="Vous avez déjà voté pour"><img src="/images/arrow_right_grey.png" /></a>';
                        break;

                    case 'UP_TOPIC':
                        $html = '<a class="disabled" title="Vous avez déjà voté pour"><img src="/images/arrow_right_grey.png" /></a>';
                        break;

                    case 'DOWN_MESSAGE':
                        $html = '<a class="disabled" title="Vous avez déjà voté contre"><img src="/images/arrow_left_grey.png" /></a>';
                        break;

                    case 'DOWN_TOPIC':
                        $html = '<a class="disabled" title="Vous avez déjà voté contre"><img src="/images/arrow_left_grey.png" /></a>';
                        break;
                }
            }
        }
        
        return $html;
    }
    
    private function buildHTML($type, $messageId)
    {
        $html = '';
        switch($type)
        {
            case 'UP_MESSAGE':
                $html = '<a href="'.$this->view->url(array('message' => $messageId
                                                    ), 'incrementMessageVote').'" class="increment" title="Voter pour"><img src="/images/arrow_right_orange.gif" /></a>';
                break;

            case 'UP_TOPIC':
                $html = '<a href="'.$this->view->url(array('topic' => $messageId
                                                    ), 'incrementTopicVote').'" class="increment" title="Voter pour"><img src="/images/arrow_right_orange.gif" /></a>';
                break;

            case 'DOWN_MESSAGE':
                $html = '<a href="'.$this->view->url(array('message' => $messageId
                                                    ), 'decrementMessageVote').'" class="decrement" title="Voter contre"><img src="/images/arrow_left_orange.gif" /></a>';
                break;

            case 'DOWN_TOPIC':
                $html = '<a href="'.$this->view->url(array('topic' => $messageId
                                                    ), 'decrementTopicVote').'" class="decrement" title="Voter contre"><img src="/images/arrow_left_orange.gif" /></a>';
                break;
        }
        return $html;
    }
}
?>
