<?php

class UserController extends Zend_Controller_Action
{
    public function preDispatch()
    {
        $auth = Zend_Auth::getInstance();
        
        if (!$auth->hasIdentity()) 
        {
            if($this->_request->getActionName() != 'login' && $this->_request->getActionName() != 'register')
                $this->_redirect('/default/user/login');
        }
    }

    public function init()
    {
    }

    public function indexAction()
    {
        $auth = Zend_Auth::getInstance();
        
        if (!$auth->hasIdentity()) 
        {
            $this->_redirect('/default/user/login');
        }
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
          // if POST data has been submitted
          
          if ($request->isPost()) {
              // if the Register form has been submitted and the submitted data is valid
              if (isset($_POST['register']) && $form->isValid($_POST)) {
                  $data = $form->getValues();
                  
                  if ($users->getSingleWithEmail($data['email']) != null) {
                      // if the email already exists in the database
                      $this->view->error = 'Email already taken';
                  } else if ($users->getSingleWithUsername($data['username']) != null) {
                      // if the username already exists in the database
                      $this->view->error = 'Username already taken';
                  } else if ($data['email'] != $data['emailAgain']) {
                      // if both emails do not match
                      $this->view->error = 'Both emails must be same';
                  } else if ($data['password'] != $data['passwordAgain']) {
                      // if both passwords do not match
                      $this->view->error = 'Both passwords must be same';
                  } else {
                      
                      // everything is OK, let's send email with a verification string
                      // the verifications string is an sha1 hash of the email
                      $mail = new Zend_Mail();
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
                      } else {
                          
                          // email sent successfully, let's add the user to the database
                          unset($data['emailAgain'], $data['passwordAgain'], $data['register']);
                          $data['salt'] = $this->_helper->RandomString(40);
                          $data['role'] = 'user';
                          $data['status'] = 'pending';
                          $users->add($data);
                          $this->view->success = 'Successfully registered';
                      }
                  }
              }
          }
          $this->view->form = $form;
      }
    
    public function loginAction()
    {
        $form = new Default_Form_UserLogin();
        $this->view->message = null;

        if($this->getRequest()->isPost()) 
        {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) 
            {
                if ($this->_process($form->getValues())) 
                {
                    // We're authenticated! Redirect to the home page
                    $this->_redirect('/');
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
                $adapter->setIdentity($values['username']); 
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
        	$this->_redirect('/');
    }
}

