<?php

class Administration_SahabaController extends Zend_Controller_Action {

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
                $story = $form->getValue('sahaba_story');
                $sahabas = $form->getValue('sahabas_values');
                $sahabasArray = explode(",", $sahabas);
                
                $modelStory = new Api_Model_Story();
                $modelSahaba = new Api_Model_Sahaba();
                $modelSahabaStory = new Api_Model_SahabaStory();
                
                $modelSahaba->getAdapter()->beginTransaction();
                
                $storyId = $modelStory->addStory($story);
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
}
