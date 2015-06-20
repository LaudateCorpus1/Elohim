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
            //Zend_Debug::dump($address); exit;
            
            // Check if address already exists
            /*if()
            {
                $this->view->error = "L'adresse n'est pas valide ou n'a pas été reconnue sur la carte";
            }
            else*/ if ($form->isValid($formData))
            {
                $this->view->error = null;
                $modelAddress = new Default_Model_Address();
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

