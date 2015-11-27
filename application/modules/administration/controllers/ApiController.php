<?php

class Administration_ApiController extends Zend_Controller_Action {

    public function preDispatch()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) 
        {
            if($this->_request->getActionName() != 'login')
                $this->_redirect('/administration/admin/login');
        }
    }

    public function init()
    {
        $this->_helper->layout->setLayout('admin_layout');
    }

    public function addstoryAction() 
    {
        $form = new Administration_Form_AddSahabaStory();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $source = $form->getValue('sahaba_story_source');
                $story = $form->getValue('sahaba_story');
                $comment = $form->getValue('sahaba_comment');
                $sahabas = $form->getValue('sahabas_values');
                $sahabasArray = explode(",", $sahabas);
                
                $modelStory = new Api_Model_Story();
                $modelSahaba = new Api_Model_Sahaba();
                $modelSahabaStory = new Api_Model_SahabaStory();
                
                $modelSahaba->getAdapter()->beginTransaction();
                
                $storyId = $modelStory->addStory($story, $source, $comment);
                $error = false;
                foreach($sahabasArray as $sahaba) {
                    $sahabaId = $modelSahaba->doesExist($sahaba);
                    if ($sahabaId !== false) {
                        $modelSahabaStory->addRow($storyId, $sahabaId);
                    } else {
                        $sahabaId = $modelSahaba->addSahaba($sahaba);
                        $modelSahabaStory->addRow($storyId, $sahabaId);
                    }
                }
                if(!$error) {
                    $modelSahaba->getAdapter()->commit();
                    $sahabaString = $sahabas;
                    if(strrpos($sahabas, ',') != false) {
                        $sahabaString = substr_replace($sahabas, ' et ', strrpos($sahabas, ','), 1);
                        $sahabaString = str_replace(',', ', ', $sahabaString);
                    }
                    
                    $session = new Zend_Session_Namespace('islamine');
                    $session->storiesToPublishToDevices[] = array('title' => 'Islamic Reminder', 'message' => 'Nouvelle anecdote sur '.$sahabaString);
                                        
                    // Add "is new" row for each device
                    $modelDevice = new Api_Model_Device();
                    $devices = $modelDevice->get();
                    $modelDeviceStory = new Api_Model_DeviceStory();
                    foreach($devices as $device)
                    {
                        $modelDeviceStory->addRow($device->id, $storyId);
                    }
                    
                    $this->_redirect('/myadmin1337');
                }
            }
        }
        $this->view->form = $form;
    }
    
    public function autocompleteAction()
    {
        $modelSahaba = new Api_Model_Sahaba();
        $res = $modelSahaba->search($this->_getParam('term'));
        $this->_helper->json(array_values($res));
    }
    
    public function addreminderAction() 
    {
        $form = new Administration_Form_AddReminder();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $title = $form->getValue('reminder_title');
                $text = $form->getValue('reminder_text');
                $categoryId = $form->getValue('reminder_category');
                
                $model = new Api_Model_Reminder();
                $reminderId = $model->add($title, $text, $categoryId);
                
                $session = new Zend_Session_Namespace('islamine');
                $session->remindersToPublishToDevices[] = array('title' => 'Islamic Reminder', 'message' => 'Nouveau rappel : '.$title);
                                
                // Add "is new" row for each device
                $modelDevice = new Api_Model_Device();
                $devices = $modelDevice->get();
                $modelDeviceReminder = new Api_Model_DeviceReminder();
                foreach($devices as $device)
                {
                    $modelDeviceReminder->addRow($device->id, $reminderId);
                }
                
                $this->_redirect('/myadmin1337');
            }
        }
        $this->view->form = $form;
    }
    
    function publishAction() {
        $session = new Zend_Session_Namespace('islamine');
        $count = 0;
        $isReminder = false;
        $isStory = false;
        $data = null;
        
        //------------------------------
        // The recipient registration IDs
        // that will receive the push
        // (Should be stored in your DB)
        // 
        // Read about it here:
        // http://developer.android.com/google/gcm/
        //------------------------------
        if(isset($session->remindersToPublishToDevices)) {
            $count += count($session->remindersToPublishToDevices);
            $isReminder = true;
        }
        if(isset($session->storiesToPublishToDevices)) {
            $count += count($session->storiesToPublishToDevices);
            $isStory = true;
        }
        
        if($count == 1) {
            $data = $isReminder ? $session->remindersToPublishToDevices[0] : $session->storiesToPublishToDevices[0];
        }
        else if($count > 1) {
            $message = '';
            if($isReminder) {
                $message .= count($session->remindersToPublishToDevices).' nouveau(x) rappel(s)';
            }
            if($isStory) {
                $message = empty($message) ? $message : $message.' et ';
                $message .= count($session->storiesToPublishToDevices).' nouvelle(s) anecdote(s)';
            }
            $data = array('title' => 'Islamic Reminder', 'message' => ucfirst($message));
        }
        
        if($count > 0 && !empty($data)) {
            unset($session->remindersToPublishToDevices);
            unset($session->storiesToPublishToDevices);
            
            $modelDevice = new Api_Model_Device();
            $ids = $modelDevice->getGCMRegistrationIds();
            Islamine_Api::sendGoogleCloudMessage($data, $ids);
        }
        
        $this->_redirect('/myadmin1337');
    }
}
