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
//            67 bis Avenue Albert Petit, 92220 Bagneux
            $address = $formData['formatted_address'];
            if(empty($formData['formatted_address'])) 
            {
                $address = $formData['search_content'];
                $mosques = $modelMosque->getByFormattedAddress($formData['search_content']);
            }
            else 
            {
                $mosques = $modelMosque->getByLocation(
                                        $formData['country'],
                                        $formData['route'],
                                        $formData['street_number'],
                                        $formData['locality'],
                                        $formData['sublocality'],
                                        $formData['administrative_area_level_1'],
                                        $formData['administrative_area_level_2'],
                                        $formData['administrative_area_level_3']);
            }
            
            if($mosques != null)
            {
                $this->view->count = count($modelMosque->fetchAll($mosques));
                $page = new Islamine_Paginator(new Zend_Paginator_Adapter_DbSelect($mosques));
                $page->setPageRange(5);
                $page->setCurrentPageNumber($this->_getParam('page', 1));
                $page->setItemCountPerPage(100);
                $this->view->mosques = $page;
            }
            
            $this->view->address = $address;
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
            //Zend_Debug::dump($formData); exit;
            $formData = $this->_request->getPost();
            if(empty($formData['formatted_address'])
               || empty($formData['route'])
               || empty($formData['locality'])
               || empty($formData['country']))
            {
                $this->view->error = "L'adresse n'est pas valide";
            }
            else if ($form->isValid($formData))
            {
                $this->view->error = null;
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

