<?php

class MosqueController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout->setLayout('mosque_layout');
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
            //Zend_Debug::dump($formData); exit;
            // The Islamic Cultural Centre and The London Central Mosque
            $address = $formData['formatted_address'];
            if(empty($formData['formatted_address'])) 
            {
                $address = $formData['search_content'];
                $mosques = $modelMosque->getByFormattedAddress($formData['search_content']);
            }
            else 
            {
                $mosques = $modelMosque->getByLocation($formData['country'], $formData['locality'], $formData['route'], $formData['street_number']);
            }
            
            if($mosques != null)
            {
                $page = new Islamine_Paginator(new Zend_Paginator_Adapter_DbSelect($mosques));
                $page->setPageRange(5);
                $page->setCurrentPageNumber($this->_getParam('page', 1));
                $page->setItemCountPerPage(100);
                $this->view->mosques = $page;
                //$this->view->headScript()->appendScript("var mosques = 'ok';");
            }
            
            $this->view->address = $address;
        }
    }
    
    public function addAction()
    {
        $form = new Default_Form_MosqueCreate();
        if($this->_request->isPost()) 
        {
            $formData = $this->_request->getPost();
            if ($form->isValid($formData))
            {
                $modelMosque = new Default_Model_Mosque();
                $modelAddress = new Default_Model_Address();
                $addressId = $modelAddress->addAddress($formData);
                if($addressId != null)
                {
                    $sessionNamespace = new Zend_Session_Namespace('Zend_Searchs');
                    $sessionNamespace->data = $formData;
                    $modelMosque->addMosque($formData, $addressId);
                    $this->_redirect('/mosque/find');
                }
            }
        }
        
        $this->view->form = $form;
    }
}

