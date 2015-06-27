<?php

class MosqueController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout->setLayout('mosque_layout');
        
        if ($this->_request->isXmlHttpRequest()) {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();    //disable layout for ajax
        }
    }
    
    public function indexAction()
    {
        $sessionNamespace = new Zend_Session_Namespace('Zend_Searchs');
        $form = new Default_Form_SearchMosque();
        
        if($this->_request->isPost())
        {
            $formData = $this->_request->getPost();
            if ($form->isValid($formData))
            {
                $sessionNamespace->data = $formData;
                $this->_redirect('/mosque/find');
            }
        }
        
        $this->view->form = $form;
    }
    
    public function searchAction()
    {
        $this->view->form = new Default_Form_SearchMosque();
        
        $sessionNamespace = new Zend_Session_Namespace('Zend_Searchs');
        if(isset($sessionNamespace->data))
        {
            $modelMosque = new Default_Model_Mosque();
            $formData = $sessionNamespace->data;
            unset($sessionNamespace->data);
            
            if(!isset($formData['street_number']))
                $formData['street_number'] = '';
            if(!isset($formData['route']))
                $formData['route'] = '';
            
            $mosques = $modelMosque->getByLocalizedLocation(
                    $formData['street_number'],
                    $formData['route'],
                    $formData['locality'],
                    $formData['search_content']);
                
            if($mosques != null)
            {
                $this->view->count = count($modelMosque->fetchAll($mosques));
                $page = new Islamine_Paginator(new Zend_Paginator_Adapter_DbSelect($mosques));
                $page->setPageRange(5);
                $page->setCurrentPageNumber($this->_getParam('page', 1));
                $page->setItemCountPerPage(100);
                $this->view->mosques = $page;
            }
            
            $this->view->address = $formData['search_content'];
        }
    }
    
    public function byrouteAction()
    {
        $address = $this->getRequest()->getParam('address');
        $modelMosque = new Default_Model_Mosque();
        
        if ($this->getRequest()->isXmlHttpRequest())
        {
            $response = array();
            $mosques = $modelMosque->getByLocalizedRoute($address);
            if($mosques != null)
            {
                foreach($mosques as $mosque)
                {
                    $response[] = array('name' => $mosque->name, 'address' => $mosque->formatted);
                }
                echo Zend_Json::encode($response);
            }
        }
    }
    
    public function saveAction()
    {
        $id = $this->_getParam('id');
        $modelMosque = new Default_Model_Mosque();
        $form = new Default_Form_MosqueCreate();
        
        /*if(isset($id) && $id != null)
        {
            $mosque = $modelMosque->get($id);
            if($mosque != null)
            {
                $form->populate(array(
                    'mosque_name' => $mosque->name
               ));
            }
        }*/
        
        if($this->_request->isPost()) 
        {
            $formData = $this->_request->getPost();
            $address = Islamine_Geocode::geocode($formData['form_mosque_address']);
            $modelAddress = new Default_Model_Address();
            //Zend_Debug::dump($address); exit;
            
            // Check if address already exists
            if($modelAddress->doesExist($address['formatted']))
            {
                $this->view->error = 'Une mosquée avec cette adresse existe déjà';
            }
            else if ($form->isValid($formData))
            {
                $this->view->error = null;
                $addressId = $modelAddress->addAddress($address);
                if($addressId != null)
                {
                    $sessionNamespace = new Zend_Session_Namespace('Zend_Searchs');
                    $formData['search_content'] = $address['formatted'];
                    $sessionNamespace->data = $formData;
                    $modelMosque->addMosque($formData, $addressId);
                    $this->_redirect('/mosque/find');
                }
            }
        }
        
        $this->view->form = $form;
    }
}

