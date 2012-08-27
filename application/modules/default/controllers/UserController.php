<?php

class UserController extends Zend_Controller_Action
{
    public function preDispatch()
    {
    }

    public function init()
    {
        $this->_helper->layout->setLayout('layout');
        
        if ($this->_request->isXmlHttpRequest()) {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();    //disable layout for ajax
        }
    }

    public function indexAction()
    {
        $userId = $this->_getParam('id');
        $model_user = new Model_User();
        $this->view->user = $model_user->getById($userId);
        $this->view->stats = $model_user->getKarmaStats($userId);
        
        $edit = false;
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            if($auth->getIdentity()->id == $this->view->user->id)
                $edit = true;
        }
        $this->view->edit = $edit;
    }

   public function registerAction()
   {
        // redirect logged in users
        $auth = Zend_Auth::getInstance();
                
        if ($auth->hasIdentity()) {
                $this->_redirect('/');
        }
          
          $request = $this->getRequest();
          $form = new Default_Form_UserRegister();
          
          if ($request->isPost()) {
              if ($form->isValid($_POST)) {
                  $data = $form->getValues();
                  
                  if ($data['password'] != $data['passwordAgain']) {
                      $this->view->error = 'Les deux mots de passe doivent être les mêmes';
                  } else {
                      
                      // everything is OK, let's send email with a verification string
                      // the verifications string is an sha1 hash of the email
                      /*if(!$this->_helper->alertMail->send('Inscription Islamine', $data['username'].',
  Merci de vous être inscrit sur http://www.islamine.com. Pour activer votre compte
  merci de cliquer sur le lien suivant:
  http://islamine.localhost/login/email-verification?str=' . sha1($data['email'])
  . '
  Cordialement,
  L\'équipe d\'Islamine', $data['email']))
                      {
                         $this->view->error = 'Failed to send email to the address you provided'; 
                      }
                      else*/ {
                          
                          // email sent successfully, let's add the user to the database;
                          $data['login'] = $data['username'];
                          unset($data['Default_Form_UserRegister_csrf']);
                          unset($data['username'], $data['passwordAgain'], $data['register']);
                          //$data['salt'] = $this->_helper->RandomString(40);
                          $data['role'] = 'member';
                          $data['status'] = 'pending';
                          $data['avatar'] = 'userpic.jpeg';
                          $data['date_created'] = date('Y-m-d H:i:s');
                          
                          $model_user = new Model_User();
                          $model_user->add($data);
                          $this->view->success = 'Inscription réussie.';
                      }
                  }
              }
              else
              {
                    $elements = $form->getElements();
                    foreach($elements as $element)
                    {
                        $errors = $element->getErrors();
                        if(count($errors) > 0)
                        {
                            $element->setAttrib('class', 'error');
                        }
                    }
               }
          }
          $this->view->form = $form;
      }
    
    public function loginAction()
    {
        $form = new Default_Form_UserLogin();
        $url = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
        $form->populate(array('uri' => $url));
        $this->view->message = null;

        if($this->getRequest()->isPost()) 
        {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData))
            {
                if ($this->_process($form->getValues())) 
                {
                    // We're authenticated! Redirect to the home page
                    if(isset($formData['uri']))
                        $this->_redirect($formData['uri']);
                    else {
                        $this->_redirect('/');
                    }
                }
                else
                {
                    $this->view->message = "Echec de l'identification";
                }
            }
            else
            {
                $elements = $form->getElements();
                foreach($elements as $element)
                {
                    $errors = $element->getErrors();
                    if(count($errors) > 0)
                    {
                        $element->setAttrib('class', 'error');
                    }
                }
            }
        }
        $this->view->form = $form;
    }

    protected function _process($values)
    {
        // Get our authentication adapter and check credentials
                $adapter = $this->_getAuthAdapter();
                $adapter->setIdentity($values['username']); 
                $adapter->setCredential($values['password']);
        
                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($adapter);
                if ($result->isValid()) 
                {
                    if ($values['rememberMe'] == 1) {
                        // remember the session for 604800s = 7 days
                        Zend_Session::rememberMe(604800);
                    } else {
                        Zend_Session::forgetMe();
                    }

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
        $session = new Zend_Session_Namespace('islamine');
        $this->_redirect($session->redirect, array('prependBase' => false));
    }
    
    public function listtopicsAction()
    {
        $username = $this->_getParam('username');
        $model_user = new Model_User();
        $topics = $model_user->getTopicsByLogin($username);
        $this->view->user = $model_user->getByLogin($username);
        
        $page = Islamine_Paginator::factory($topics);
        $page->setPageRange(5);
        $page->setCurrentPageNumber($this->_getParam('page',1));
        $page->setItemCountPerPage(20);
        $this->view->user_topics = $page;
    }
    
    public function listmessagesAction()
    {
        $username = $this->_getParam('username');
        $model_user = new Model_User();
        $messages = $model_user->getMessagesByLogin($username);
        $this->view->user = $model_user->getByLogin($username);
        
        $message_pagination = Islamine_Paginator::factory($messages);
        $message_pagination->setPageRange(5);
        $message_pagination->setCurrentPageNumber($this->_getParam('page',1));
        $message_pagination->setItemCountPerPage(20);
        $this->view->user_messages = $message_pagination;
    }
    
    public function editAction()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $username = $this->_getParam('username');
            $model_user = new Model_User();
            $user = $model_user->getByLogin($username);
            if($auth->getIdentity()->id == $user->id)
            {
                $password = $user->password;
                unset($user->password);
                
                $form = new Default_Form_UserEdit();
                $form->populate($user->toArray());
                if ($this->getRequest()->isPost()) 
                {
                    $errors = false;
                    $formData = $this->getRequest()->getPost();
                    if ($form->isValid($formData)) 
                    {
                        // Upload de l'avatar
                        $uploaded_file = new Zend_File_Transfer_Adapter_Http();
                        $file_name = $uploaded_file->getFileName('avatar', false);
                        if(!empty ($file_name))
                        {
                            $destinationPath = APPLICATION_UPLOADS_DIR.'/'.$auth->getIdentity()->id;
                            if(!file_exists($destinationPath))
                                mkdir($destinationPath);
                            
                            $uploaded_file->setDestination($destinationPath);
                            $extension = mb_substr($file_name, mb_strrpos($file_name, '.') +1);
                            $uploaded_file->addFilter('Rename', array('target' => $destinationPath.'/avatar.'.$extension,
                                                  'overwrite' => true));

                            if (!$uploaded_file->receive())
                            {
                                $errors = true;
                                $form->getElement('avatar')->addError($uploaded_file->getMessages());
                            }
                            
                            //$filter = new Zend_Filter_ImageSize(); 
                            //$output = $filter->setHeight(128)->setWidth(128)->setOverwriteMode(Zend_Filter_ImageSize::OVERWRITE_ALL)->filter($destinationPath.'/avatar.'.$extension);
                                                        
                            $data = $form->getValues();
                            $data['avatar'] = 'users/'.$auth->getIdentity()->id.'/avatar.'.$extension;
                        }
                        else
                        {
                            $data = $form->getValues();
                            unset($data['avatar']);
                        }
                        
                        if($data['password'] == '' || $data['oldPassword'] == '' || $data['passwordAgain'] == '')
                        {
                            unset($data['password']);
                            unset($data['oldPassword']);
                            unset($data['passwordAgain']);
                        }
                        else
                        {
                            if($data['password'] != $data['passwordAgain'])
                            {
                                $form->getElement('password')->addError('Les deux mots de passe doivent être les mêmes');
                                $form->getElement('passwordAgain')->addError('Les deux mots de passe doivent être les mêmes');
                                $errors = true;
                            }
                            if(md5($data['oldPassword']) != $password)
                            {
                                $form->getElement('oldPassword')->addError('Votre ancien mot de passe n\'est pas correct');
                                $errors = true;
                            }
                        }
                        
                        foreach ($data as $key => $value)
                        {
                            if($value == '')
                                unset($data[$key]);
                        }
                        
                        if(!$errors)
                        {
                            if(isset($data['password']))
                            {
                                unset($data['oldPassword']);
                                unset($data['passwordAgain']);
                                $data['password'] = md5($data['password']);
                            }
                            unset($data['Default_Form_UserEdit_csrf']);
                            
                            $model_user->updateUser($data, $user->id);
                            $this->_redirect('/users/'.$user->id.'/' . $username);
                        }
                    }
                    
                    $form->populate($form->getValues());
                }
                
                $this->view->form = $form;
            }
            else
                throw new Exception (null, 404);
        }
    }
    
    public function showimageAction()
    {
        $this->view->id = $id = $this->_getParam('id');
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $image = file_get_contents(APPLICATION_UPLOADS_DIR.'/'.$id.'/avatar.jpg');
        $this->getResponse()->clearBody ();
        $this->getResponse()->setHeader('Content-Type', 'image/jpeg');
        $this->getResponse()->setBody($image);
        //header('Content-Type: image/jpeg');
    }
    
    public function favdocsAction()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $identity = $auth->getIdentity();
            $model_user = new Model_User();
            $documents = $model_user->getFavoriteDocuments($identity->id);

            $message_pagination = new Islamine_Paginator(new Zend_Paginator_Adapter_DbSelect($documents));
            $message_pagination->setPageRange(5);
            $message_pagination->setCurrentPageNumber($this->_getParam('page',1));
            $message_pagination->setItemCountPerPage(20);
            $this->view->documents = $message_pagination;
        }
    }
    
    public function listAction()
    {
        $model_user = new Model_User();
        $users = $model_user->getAll();
        
        $user_pagination = Islamine_Paginator::factory($users);
        $user_pagination->setPageRange(5);
        $user_pagination->setCurrentPageNumber($this->_getParam('page',1));
        $user_pagination->setItemCountPerPage(36);
        $this->view->users = $user_pagination;
    }
    
    public function forgotpasswordAction()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $message = 'Vous êtes déjà connecté';
            if($this->_request->isXmlHttpRequest())
                echo Zend_Json::encode(array('status' => 'error', 'message' => $message));
            else
                throw new Exception($message);
        }
        else
        {
            if ($this->_request->isXmlHttpRequest()) 
            {
                $data = $this->getRequest()->getPost();
                $email = $data['email'];
                $model_user = new Model_User();
                if($model_user->doesEmailExist($email))
                {
                    $newPassword = substr(md5(date('Ymdhis')),0,8);
                    $res = $model_user->updateUserByEmail(array('password' => $newPassword), $email);
                    
                    $this->_helper->alertMail->send('Récupération mot de passe Islamine', 'Bonjour,

Voici votre nouveau mot de passe : '.$newPassword.'
Nous vous conseillons de le changer dans les plus brefs délais.

Cordialement,
L\'équipe d\'Islamine', $email);

                    echo Zend_Json::encode(array('status' => 'ok', 'message' => 'Un e-mail avec un nouveau mot de passe a été envoyé'));
                }
                else
                    echo Zend_Json::encode(array('status' => 'error', 'message' => 'Cet e-mail n\'existe pas'));
            }
        }
    }
}

