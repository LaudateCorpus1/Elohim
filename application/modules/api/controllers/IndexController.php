<?php

class Api_IndexController extends Zend_Rest_Controller {

    public function init() {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
    }

    public function indexAction() {
    }

    public function getAction() {
    }

    public function postAction() {
        $rawData = $this->getRequest()->getRawBody();
        $data = Zend_Json::decode($rawData);
        $model = new Api_Model_Device();
        $success = $model->addGCMRegistrationId($data['registration_id'], $data['platform'], $data['os_version']);
               
        $res = array('status' => $success == null ? 'Fail' : 'Success');
        $this->_helper->json($res);
    }

    public function putAction() {
        
    }

    public function deleteAction() {
        
    }
}
