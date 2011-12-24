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
            $modelNotification = new Model_Notification();
            if($request->getModuleName() == 'forum' 
                && $request->getControllerName() == 'topic' 
                && $request->getActionName() == 'show')
            {
                $modelNotification->updateNotifications(array('beenRead' => true), $request->getParam('topic'));
            }
            
            $notifications = $modelNotification->getAllUnreadByUser($auth->getIdentity()->id);
            $return = $notifications;
        }
        
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');
        $view->notification = $return;
    }

}
