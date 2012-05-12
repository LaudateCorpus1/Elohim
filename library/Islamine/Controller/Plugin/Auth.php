<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Plugin d'authentification
 *
 * @author jeremie
 */

class Islamine_Controller_Plugin_Auth extends Zend_Controller_Plugin_Abstract	
{
    /**
     * @var Zend_Auth instance 
     */
    private $_auth;

    /**
     * @var Zend_Acl instance 
     */
    private $_acl;
    
    /**
     * @var Tableau des actions qui nécessitent un certain niveau de karma
     */
    //private $_karma_array;
    

    /**
     * Chemin de redirection lors de l'échec d'authentification
     */
    const FAIL_AUTH_MODULE     = 'default';
    const FAIL_AUTH_ACTION     = 'login';
    const FAIL_AUTH_CONTROLLER = 'user';

    /**
     * Chemin de redirection lors de l'échec de contrôle des privilèges
     */
    const FAIL_ACL_MODULE     = 'default';
    const FAIL_ACL_ACTION     = 'error';
    const FAIL_ACL_CONTROLLER = 'error';
    
    /**
     * Chemin de redirection lors de l'échec de contrôle des privilèges
     */
    const ERROR_MODULE     = 'default';
    const ERROR_ACTION     = 'error';
    const ERROR_CONTROLLER = 'error';
    
    /**
     * Chemin de redirection lors de l'échec de contrôle des privilèges de karma
     */
    const FAIL_KARMA_MODULE     = 'forum';
    const FAIL_KARMA_ACTION     = 'karma';
    const FAIL_KARMA_CONTROLLER = 'error';
    
    

    /**
     * Constructeur
     */
    public function __construct()	
    {
        $this->_acl = Zend_Registry::get('acl');
        $this->_auth = Zend_Auth::getInstance();
        //$this->_karma_array = $this->_acl->_getKarmaPrivileges();
    }

    /**
     * Vérifie les autorisations
     * Utilise _request et _response hérités et injectés par le FC
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)	
    {
        // is the user authenticated
        if ($this->_auth->hasIdentity()) 
        {
          // yes ! we get his role
          $user = $this->_auth->getStorage()->read();
          if(is_object($user))
              $user = get_object_vars($user);
          
          $role = $user['login'].'_'.$user['id'];
          if(!$this->_acl->hasRole($role)) 
            $role = $user['role'] ;
        }
        else 
        {
            // no = guest user
            $role = 'guest';
        }

        $module     = $request->getModuleName();
        $controller = $request->getControllerName() ;
        $action     = $request->getActionName() ;
        
        if($module == 'forum')
        {
            // action/resource does not exist in ACL
            $module = self::ERROR_MODULE;
            $controller = self::ERROR_CONTROLLER;
            $action = self::ERROR_ACTION;
        }
        else
        {
        
            $front = Zend_Controller_Front::getInstance() ;
            $default = $front->getDefaultModule() ;

            // compose le nom de la ressource
            if ($module == $default)
                $resource = $controller ;
            else
                $resource = $module.'_'.$controller ;

            // est-ce que la ressource existe ?
            if (!$this->_acl->has($resource)) 
            {
                return true;
                // action/resource does not exist in ACL
                $module = self::ERROR_MODULE;
                $controller = self::ERROR_CONTROLLER;
                $action = self::ERROR_ACTION;
            } 
            else 
            {
                // contrôle si l'utilisateur est autorisé
                if (!$this->_acl->isAllowed($role, $resource, $action))
                {
                    // l'utilisateur n'est pas autorisé à accéder à cette ressource
                    // on va le rediriger
                    if (!$this->_auth->hasIdentity()) 
                    {
                        if($request->isXmlHttpRequest())
                        {
                            $this->disableLayout();
                            $this->sendAjaxResponse(array('status' => 'error', 'message' => 'Vous devez vous identifier'));
                            // redirectAndExit() cleans up, sends the headers and stopts the script
                            Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')->redirectAndExit(); 
                        }
                        else
                        {
                            // il n'est pas identifié -> module de login
                            $module = self::FAIL_AUTH_MODULE;
                            $controller = self::FAIL_AUTH_CONTROLLER;
                            $action = self::FAIL_AUTH_ACTION;
                        }
                    }
                    else 
                    {
                        $model_privileges = new Model_Privileges();
                        $privilege = $model_privileges->getMRP($module, $controller, $action);

                        if($request->isXmlHttpRequest())
                        {
                            $this->disableLayout();
                            $this->sendAjaxResponse($privilege);
                            // redirectAndExit() cleans up, sends the headers and stopts the script
                            Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')->redirectAndExit(); 
                        }
                        else
                        {
                            $request->setParam('privilege', $privilege);

                            $module = self::FAIL_KARMA_MODULE ;
                            $controller = self::FAIL_KARMA_CONTROLLER ;
                            $action = self::FAIL_KARMA_ACTION ;
                        }
                    }
                }
            }
        }

        $request->setModuleName($module) ;
        $request->setControllerName($controller) ;
        $request->setActionName($action) ;
    }
    
    protected function disableLayout()
    {
        $ViewHelper = Zend_Controller_Action_HelperBroker::getStaticHelper("ViewRenderer");
        $ViewHelper->setNoRender(true);
        Zend_Layout::getMvcInstance()->disableLayout();
    }


    protected function sendAjaxResponse($body)
    {
        if(is_object($body))
            $body = $body->toArray();
        $body['status'] = 'error';
        $json = Zend_Json::encode($body);

        // Prepare response
        $this->getResponse()
             ->setHttpResponseCode(200) // Or maybe HTTP Status 401 Unauthorized
             ->setBody($json)
             ->sendResponse();
    }
}


?>
