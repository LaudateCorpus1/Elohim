<?php

class Islamine_Controller_Plugin_Notification extends Zend_Controller_Plugin_Abstract {

    /**
     * preDispatch()
     *
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $userNotifications = $privilegeNotifications = '';
        $layout = Zend_Layout::getMvcInstance();
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $modelNotification = new Model_Notification();
            if($request->getModuleName() == 'forum' 
                && $request->getControllerName() == 'topic' 
                && $request->getActionName() == 'show')
            {
                $topicId = $request->getParam('topic');
                $modelTopic = new Forum_Model_Topic();
                $topicAuthor = $modelTopic->getAuthor($topicId);
                if($topicAuthor->userId == $auth->getIdentity()->id)
                {
                    $modelNotification->updateNotifications(array('beenRead' => true), $topicId);
                }
            }
            
            $notifications = $modelNotification->getAllUnreadByUser($auth->getIdentity()->id);
            $userNotifications = $notifications;
            
            $notificationsP = $modelNotification->getPrivilegeNotifications($auth->getIdentity()->id);
            $privilegeNotifications = $notificationsP;
            
        }
        
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');
        $view->notification = $userNotifications;
        $layout->privilegeNotifications = $this->buildHtmlPrivilegeNotifications($privilegeNotifications);
    }

    private function buildHtmlPrivilegeNotifications($privilegeNotifications)
    {
        $html = '';
        
        foreach($privilegeNotifications as $privilegeNotification)
        {
            $html .= '<div id="notify-message" style="display: none;">
                        <span>'.$privilegeNotification->message.'</span>
                        <a href="#" class="close-notify">X</a>
                     </div>';
        }
        
        return $html;
    }
}
