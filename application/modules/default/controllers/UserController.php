<?php

class UserController extends Zend_Controller_Action
{
    public function preDispatch()
    {
        $auth = Zend_Auth::getInstance();
        
        /*if (!$auth->hasIdentity()) 
        {
            if($this->_request->getActionName() != 'login' && $this->_request->getActionName() != 'register' && $this->_request->getActionName() != 'index')
                $this->_redirect('/user/login');
        }*/
    }

    public function init()
    {
        $this->_helper->layout->setLayout('forum_layout');
        
        if ($this->_request->isXmlHttpRequest()) {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();    //disable layout for ajax
        }
    }

    public function indexAction()
    {
        $username = $this->_getParam('username');
        $model_user = new Model_User();
        $this->view->user = $model_user->getByLogin($username);
        
        $auth = Zend_Auth::getInstance();
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
                    $tag_model = new Forum_Model_Tag();
                    $fav_tags = $tag_model->getFavoriteTags($user->id)->toArray();
                    /*$user_model = new Model_User();
                    $user_votes = $user_model->getVotes($user->id);*/
                    $extended_user = (object)array_merge((array)$user, array('favtags' => $fav_tags));
                    $auth->getStorage()->write($extended_user);
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
}

