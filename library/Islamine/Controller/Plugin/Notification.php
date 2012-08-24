<?php

class Islamine_Controller_Plugin_Notification extends Zend_Controller_Plugin_Abstract {

    /**
     * preDispatch()
     *
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        if (!$request->isXmlHttpRequest()) 
        {
            $userNotifications = '';
            $privilegeNotifications = null;
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
                        $modelNotification->updateNotifications(array('beenRead' => true), array('topicId' => $topicId));
                    }
                }
                else if($request->getModuleName() == 'default' 
                    && $request->getControllerName() == 'library' 
                    && $request->getActionName() == 'show')
                {
                    $documentId = $request->getParam('document');
                    $modelLibrary = new Default_Model_Library();
                    $documentAuthor = $modelLibrary->getUserId($documentId);
                    if($documentAuthor == $auth->getIdentity()->id)
                    {
                        $modelNotification->updateNotifications(array('beenRead' => true), array('documentId' => $documentId));
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
            $layout->privilegeNotifPadding = $this->htmlPrivilegeNotificationsPadding($privilegeNotifications);
        }
    }

    private function buildHtmlPrivilegeNotifications($privilegeNotifications)
    {
        $html = '';
        $i = 1;
        if(count($privilegeNotifications) > 0)
        {
            foreach($privilegeNotifications as $privilegeNotification)
            {
                $last = '';
                if($i == count($privilegeNotifications))
                    $last = 'lastnotif';
                
                $class = 'gained-notify-message';
                if($privilegeNotification->type == 'LOST-PRIVILEGE')
                    $class = 'lost-notify-message';
                
                $html .= '<div id="notify-message" class="'.$class.'" style="display: none;">
                            <span class="'.$last.'">'.$privilegeNotification->message.'</span>
                            <a href="#" class="close-notify" id="close-notify-'.$privilegeNotification->id.'">X</a>
                         </div>';
                $i++;
            }
        }
        
        return $html;
    }
    
    private function htmlPrivilegeNotificationsPadding($privilegeNotifications)
    {
        $html = '';
        
        if(count($privilegeNotifications) > 0)
        {
            $html = '<div id="notification-padding" style="display: none;">
                     </div>';
        }
        
        return $html;
    }
}
