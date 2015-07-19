<?php

class Api_ReminderController extends Zend_Rest_Controller {

    public function init() {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
    }

    private function getReminders($page = 1) {
        $model = new Api_Model_Reminder();
        $reminders = $model->getReminders($page);
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
        $page = $this->_getParam('page');
        $id = $this->_getParam('id');
        $this->_helper->json($this->getReminders($page));
    }

    public function postAction() {
        
    }

    public function putAction() {
        
    }

    public function deleteAction() {
        
    }
}
