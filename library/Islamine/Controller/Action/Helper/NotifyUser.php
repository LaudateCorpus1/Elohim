<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Islamine_Controller_Action_Helper_NotifyUser extends Zend_Controller_Action_Helper_Abstract
{
    public function direct($message, $toUserId)
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $data = array('message' => $message, 'toUserId' => $toUserId);
            $identity = $auth->getIdentity();
            $model_notification = new Model_Notification();
            $model_notification->addNotification($data);
        }
    }
}


?>
