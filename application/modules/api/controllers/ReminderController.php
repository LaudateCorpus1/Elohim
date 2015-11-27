<?php

class Api_ReminderController extends Zend_Rest_Controller {

    public function init() {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
    }

    public function indexAction() {
        $this->_helper->json($this->getReminders());
    }

    public function getAction() {
        $offset = $this->_getParam('offset');
        $ressource = $this->_getParam('get');
        $response = array();

        if($ressource == 'newreminders') {
            $platform = $this->_getParam('platform');
            $deviceId = $this->_getParam('device');
            if(!empty($deviceId) && !empty($platform)) {
                $response = $this->getUndreadReminders($platform, $deviceId);
            }
        }
        else {
            $response = $this->getReminders($offset);
        }
        
        $this->_helper->json($response);
    }

    public function postAction() {
        
    }

    public function putAction() {
        $ressource = $this->_getParam('set');
        $n = 0;

        if($ressource == 'notnew') {
            $reminderId = $this->_getParam('reminder');
            $deviceId = $this->_getParam('device');
            if(!empty($deviceId) && !empty($reminderId)) {
                $model = new Api_Model_DeviceReminder();
                $n = $model->setNotNew($deviceId, $reminderId);
            }
        }
        
        $response = array('status' => 200, 'updated' => $n);
        $this->_helper->json($response);
    }

    public function deleteAction() {
        
    }
    
    private function getReminders($offset = 0) {
        $model = new Api_Model_Reminder();
        $reminders = $model->getReminders($offset);
        $response = array();

        foreach ($reminders as $reminder) {
            $response[] = array(
                'id' => $reminder->id,
                'title' => $reminder->title,
                'text' => Islamine_String::replaceNewLinesWithBr($reminder->text),
                'category' => $reminder->category
            );
        }

        return $response;
    }
    
    private function getUndreadReminders($platform, $deviceId) {
        $response = array();
        $model = new Api_Model_DeviceReminder();
        $reminders = $model->getUnreadReminders($platform, $deviceId);
        $count = 0;
        
        foreach ($reminders as $reminder) {
            $response['remindersId'][] = $reminder->id;
            $count++;
        }
        $response['count'] = $count;
        
        return $response;
    }
}
