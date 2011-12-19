<?php

class Administration_AdminController extends Zend_Controller_Action
{

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

    public function indexAction()
    {
        $auth = Zend_Auth::getInstance();
                
        if (!$auth->hasIdentity()) {
                $this->_redirect('/administration/admin/login');
        }
        else
        {
            $user = $auth->getIdentity();
            $this->view->user = $user;
        }
    }

    public function loginAction()
    {
        $form = new Administration_Form_Login();
        $this->view->message = null;

        if($this->getRequest()->isPost()) 
        {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) 
            {
                if ($this->_process($form->getValues())) 
                {
                    // We're authenticated! Redirect to the home page
                    $this->_redirect('/administration/admin');
                }
                else
                {
                    $this->view->message = "Echec de l'identification";
                }
            }
        }
        $this->view->form = $form;
    }

    protected function _process($values)
    {
        // Get our authentication adapter and check credentials
                $adapter = $this->_getAuthAdapter();
                $adapter->setIdentity($values['login']); 
                $adapter->setCredential($values['password']);
        
                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($adapter);
                if ($result->isValid()) 
                {
                    $user = $adapter->getResultRowObject(null, 'password');
                    $auth->getStorage()->write($user);
                    return true;
                }
                return false;
    }

    protected function _getAuthAdapter()
    {
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
                $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
        
                $authAdapter->setTableName('user')
                    ->setIdentityColumn('login')
                    ->setCredentialColumn('password')
                    ->setCredentialTreatment('MD5(?)');
        
                return $authAdapter;
    }

    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        	$this->_redirect('/administration/admin');
    }

    public function addarticleAction()
    {
        $form = new Default_Form_Article();
        $this->view->message = null;

        if($this->getRequest()->isPost()) 
        {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) 
            {
                $title = $form->getValue('title');
                $content = $form->getValue('content');
                $category = $form->getValue('category');
                
                $model_article = new Model_Article();
                $model_article->addArticle($title, $content, $category);
                $this->_redirect('/administration/admin');
            }
        }
        $this->view->form = $form;
    }

    public function addcategoryAction()
    {
        // action body
    }
    
    public function managearticlesAction()
    {
        
        $article_id = $this->_getParam('id');
        if($article_id != null)
        {
            $model = new Model_Article();
            $this->view->article = $model->get($article_id);
            $this->view->comments = $model->getComments($article_id);
        }
        
        $nav = array();
        $model_category = new Model_Category();
        $categories = $model_category->getAll();
        $model_article = new Model_Article();
        
        foreach($categories as $category)
        {
            $articles = $model_article->getByCategory($category->id);
            $nav[$category->name] = $articles;
        }

        $this->view->sidebar = $nav;
    }
    
    public function managecategoriesAction()
    {
        
    }
    
    public function deletearticleAction()
    {
        $article_id = $this->_getParam('id');
        $model = new Model_Article();
        $model->deleteArticle($article_id);
        $this->_redirect('/administration/admin/managearticles');
    }
    
    public function updatearticleAction()
    {
        $article_id = $this->_getParam('id');
        $model = new Model_Article();
        $article = $model->get($article_id);
        
        $form = new Default_Form_Article();
        $this->view->message = null;

        if($this->getRequest()->isPost()) 
        {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) 
            {
                $model_article = new Model_Article();
                $data = array('title' => $form->getValue('title'),
                              'content' => $form->getValue('content'),
                              'id_category' => $form->getValue('category')
                    );
                
                $model_article->updateArticle($data, $article_id);
                $this->_redirect('/administration/admin/managearticles/id/'.$article_id);
            }
        }
        $form->setDefaultValues($article->title, $article->content, $article->id_category);
        $this->view->form = $form;
    }
}







