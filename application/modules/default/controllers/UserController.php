<?php

class UserController extends Zend_Controller_Action
{
    public function preDispatch()
    {
        $auth = Zend_Auth::getInstance();
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
          //$users = $this->_getTable('User');
          //$form = $this->_getForm('Register', $this->_helper->url('register'));
          $form = new Default_Form_UserRegister();
          
          if ($request->isPost()) {
              // if the Register form has been submitted and the submitted data is valid
              if ($form->isValid($_POST)) {
                  $data = $form->getValues();
                  
                  /*if ($users->getSingleWithEmail($data['email']) != null) {
                      // if the email already exists in the database
                      $this->view->error = 'Email already taken';
                  } else if ($users->getSingleWithUsername($data['username']) != null) {
                      // if the username already exists in the database
                      $this->view->error = 'Username already taken';
                  } else if ($data['email'] != $data['emailAgain']) {
                      // if both emails do not match
                      $this->view->error = 'Both emails must be same';
                  } else*/ if ($data['password'] != $data['passwordAgain']) {
                      // if both passwords do not match
                      $this->view->error = 'Les deux mots de passe doivent être les mêmes';
                  } else {
                      
                      // everything is OK, let's send email with a verification string
                      // the verifications string is an sha1 hash of the email
                      /*$mail = new Zend_Mail();
                      $mail->setFrom('your@name.com', 'Your Name');
                      $mail->setSubject('Thank you for registering');
                      $mail->setBodyText('Dear Sir or Madam,
      Thank You for registering at yourwebsite.com. In order for your account to be
      activated please click on the following URI:
      http://yourwebsite.com/admin/login/email-verification?str=' . sha1($data['email'])
      . '
      Best Regards,
      Your Name and yourwebsite.com staff');
                      $mail->addTo($data['email'],
                                   $data['first_name'] . ' ' . $data['last_name']);
                      
                      if (!$mail->send()) {
                          // email sending failed
                          $this->view->error = 'Failed to send email to the address you provided';
                      } else*/ {
                          
                          // email sent successfully, let's add the user to the database;
                          $data['login'] = $data['username'];
                          unset($data['username'], $data['passwordAgain'], $data['register']);
                          //$data['salt'] = $this->_helper->RandomString(40);
                          $data['role'] = 'member';
                          $data['status'] = 'pending';
                          $data['avatar'] = 'userpic.gif';
                          
                          $model_user = new Model_User();
                          $model_user->add($data);
                          $this->view->success = 'Successfully registered';
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
                            
                            $model_user->updateUser($data, $user->id);
                            $this->_redirect('/default/user/index/username/' . $username);
                        }
                    }
                    
                    $form->populate($form->getValues());
                }
                
                $this->view->form = $form;
            }
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
}

