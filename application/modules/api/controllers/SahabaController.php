<?php

class Api_SahabaController extends Zend_Rest_Controller {

    public function init() {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        /* $this->_helper->AjaxContext()
          ->addActionContext('get','json')
          ->addActionContext('post','json')
          ->addActionContext('new','json')
          ->addActionContext('edit','json')
          ->addActionContext('put','json')
          ->addActionContext('delete','json')
          ->initContext('json'); */
    }

    public function indexAction() {
        $response = array();
        $model = new Api_Model_Sahaba();
        $sahabaNames = $model->getNames();

        foreach ($sahabaNames as $sahabaName) {
            $response[] = $sahabaName->name;
        }

        $this->_helper->json($response);
    }

    private function getStories($offset, $name) {
        $response = array();
        $model = new Api_Model_Story();
        $stories = $model->getStories($offset, $name);
        $cut = $this->_getParam('cut');

        foreach ($stories as $story) {
            $sahabas = $model->getSahabasArray($story->id);
            $text = $story->text;
            $length = $this->_getParam('length');
            if ($cut == true) {
                $length = empty($length) ? 100 : $length;
                $text = Islamine_String::textWrap($text, $length);
            }
            $text = Islamine_String::replaceNewLinesWithBr($text);
            $response[] = array('id' => $story->id, 'text' => $text, 'source' => $story->source, 'authors' => $sahabas);
        }

        return $response;
    }

    public function getAction() {
        $ressource = $this->_getParam('get');
        $offset = $this->_getParam('offset');
        $id = $this->_getParam('id');
        $response = array();

        if ($ressource == 'stories') {
            if (empty($id)) {
                $response = $this->getStories($offset, $this->_getParam('name'));
            } else {
                $model = new Api_Model_Story();
                $story = $model->getById($id);
                $sahabas = $model->getSahabasArray($id);
                $text = Islamine_String::replaceNewLinesWithBr($story->text);
                $response = array('id' => $id, 'text' => $text, 'source' => $story->source, 'authors' => $sahabas);
            }
        }

        $this->_helper->json($response);
    }

    public function postAction() {
        
    }

    public function putAction() {
        
    }

    public function deleteAction() {
        
    }

}
