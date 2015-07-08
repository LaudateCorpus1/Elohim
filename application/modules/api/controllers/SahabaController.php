<?php

class Api_SahabaController extends Zend_Rest_Controller {

    public function init() {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        /*$this->_helper->AjaxContext()
                ->addActionContext('get','json')
                ->addActionContext('post','json')
                ->addActionContext('new','json')
                ->addActionContext('edit','json')
                ->addActionContext('put','json')
                ->addActionContext('delete','json')
                ->initContext('json');*/
    }

    public function indexAction() {
        $response = array();
        $model = new Api_Model_Story();
        $stories = $model->getWithLimit();
        
        foreach ($stories as $story) {
            $response[] = array('id' => $story->id, 'text' => $story->text);
        }
        
        $this->_helper->json($response);
    }

    public function getAction() {
        
    }

    public function postAction() {
        
    }

    public function putAction() {
        
    }

    public function deleteAction() {
        
    }
}
