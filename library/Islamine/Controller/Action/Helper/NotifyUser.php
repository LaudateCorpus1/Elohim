<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Islamine_Controller_Action_Helper_NotifyUser extends Zend_Controller_Action_Helper_Abstract
{
    public function direct($message, $toUserId, $topicId = null, $messageId = null, $type = null)
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $data = array('message' => $message, 'toUserId' => $toUserId);
            if($topicId != null)
                $data['topicId'] = $topicId;
            if($messageId != null)
                $data['messageId'] = $messageId;
            if($type != null)
                $data['type'] = $type;
            
            $data['fromUserId'] = $auth->getIdentity()->id;
            
            $model_notification = new Model_Notification();
            $model_notification->addNotification($data);
        }
    }
}


?>
