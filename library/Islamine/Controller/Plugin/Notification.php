<?php

class Islamine_Controller_Plugin_Notification extends Zend_Controller_Plugin_Abstract {

    /**
     * preDispatch()
     *
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $return = '';
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $model_notification = new Model_Notification();
            $notifications = $model_notification->getAllUnreadByUser($auth->getIdentity()->id);
            $return = $notifications;
        }
        
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');
        $view->notification = $return;
    }

}
