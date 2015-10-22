<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ContactController
 *
 * @author Jérémie
 */
class Api_ContactController extends Zend_Rest_Controller {

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
        $mailRes = $this->_helper->alertMail($data['from'], '[IslamicReminder]'.$data['subject'], $data['body']);
        
        $res = array('status' => $mailRes == null ? 'Fail' : 'Success');
        $this->_helper->json($res);
    }

    public function putAction() {
        
    }

    public function deleteAction() {
        
    }
}
