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
        else if($ressource == 'newstories') {
            $platform = $this->_getParam('platform');
            $deviceId = $this->_getParam('device');
            if(!empty($deviceId) && !empty($platform)) {
                $response = $this->getNewStories($platform, $deviceId);
            }
        }
        else {
            $response = $this->getSahabas($offset);
        }

        $this->_helper->json($response);
    }

    public function postAction() {
        
    }

    public function putAction() {
        $ressource = $this->_getParam('set');
        $n = 0;

        if($ressource == 'notnew') {
            $storyId = $this->_getParam('story');
            $deviceId = $this->_getParam('device');
            if(!empty($deviceId) && !empty($storyId)) {
                $model = new Api_Model_DeviceStory();
                $n = $model->setNotNew($deviceId, $storyId);
            }
        }
        
        $response = array('status' => 200, 'updated' => $n);
        $this->_helper->json($response);
    }

    public function deleteAction() {
        
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
            $response[] = array('id' => $story->id, 'text' => $text, 'source' => $story->source, 'comment' => $story->comment, 'authors' => $sahabas);
        }

        return $response;
    }
    
    private function getSahabas($offset) {
        $response = array();
        $model = new Api_Model_Sahaba();
        $sahabas = $model->get($offset);

        foreach ($sahabas as $sahaba) {
            $response[] = array('id' => $sahaba->id, 'name' => $sahaba->name, 'bio' => $sahaba->bio);
        }
        return $response;
    }
    
    private function getNewStories($platform, $deviceId) {
        $response = array();
        $model = new Api_Model_DeviceStory();
        $modelSahabaStory = new Api_Model_SahabaStory();
        $stories = $model->getUnreadStories($platform, $deviceId);
        $count = 0;
        foreach ($stories as $story) {
            $sahabaIds = $modelSahabaStory->getSahabaIds($story->id);
            $sahabaIdsArray = array();
            foreach ($sahabaIds as $sahabaId) {
                $sahabaIdsArray[] = $sahabaId->sahaba_id;
            }

            $response['stories'][] = array('id' => $story->id, 'sahabaIds' => $sahabaIdsArray);
            $count++;
        }
        $response['count'] = $count;
        
        return $response;
    }
}
