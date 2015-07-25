<?php

class Api_ReminderController extends Zend_Rest_Controller {

    public function init() {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
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

    public function indexAction() {
        $this->_helper->json($this->getReminders());
    }

    public function getAction() {
        $offset = $this->_getParam('offset');
        $id = $this->_getParam('id');
        $this->_helper->json($this->getReminders($offset));
    }

    public function postAction() {
        
    }

    public function putAction() {
        
    }

    public function deleteAction() {
        
    }
}
