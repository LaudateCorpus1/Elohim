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
    private $_karma_array;
    

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
     * Chemin de redirection lors de l'échec de contrôle des privilèges de karma
     */
    const FAIL_KARMA_MODULE     = 'forum';
    const FAIL_KARMA_ACTION     = 'karma';
    const FAIL_KARMA_CONTROLLER = 'error';
    
    

    /**
     * Constructeur
     */
    public function __construct(Islamine_Acl $acl = null)	
    {
        $this->_acl = Zend_Registry::get('acl');
        $this->_auth = Zend_Auth::getInstance();
        $this->_karma_array = $this->_acl->_getKarmaPrivileges();
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

        $front = Zend_Controller_Front::getInstance() ;
        $default = $front->getDefaultModule() ;

        // compose le nom de la ressource
        if ($module == $default)
            $resource = $controller ;
        else
            $resource = $module.'_'.$controller ;

        // est-ce que la ressource existe ?
        if (!$this->_acl->has($resource))
            $resource = null;
        
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
                    $this->sendAjaxResponse(array('error_message' => 'Vous devez vous identifier'));
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
                // On regarde si l'action non autorisée est une action qui demande un niveau de karma
                $allow = true;
                $nb_array = 0;
                $dPrivilege = null;
                foreach($this->_karma_array as $privilege)
                {
                    $karma_module = $privilege->module;
                    $karma_resource = $privilege->resource;
                    $karma_privilege = $privilege->privilege;
                    if($karma_module == $module && $karma_resource == $controller && $karma_privilege == $action)
                    {
                        $allow = $this->allowKarmaAction($privilege);
                        $dPrivilege = $privilege;
                        break;
                    }
                    $nb_array++;
                }
                
                // Ce n'est pas une action qui nécessite un niveau de karma, l'utilisateur n'a donc pas les droits
                if($nb_array == count($this->_karma_array))
                {
                    // il est identifié -> error de privilèges
                    $module = self::FAIL_ACL_MODULE ;
                    $controller = self::FAIL_ACL_CONTROLLER ;
                    $action = self::FAIL_ACL_ACTION ;
                }
                else // L'action nessécite un niveau de karma
                {
                    // Son niveau de karma n'est pas suffisant
                    if(!$allow)
                    {
                        if($request->isXmlHttpRequest())
                        {
                            $this->disableLayout();
                            $this->sendAjaxResponse($dPrivilege);
                            // redirectAndExit() cleans up, sends the headers and stopts the script
                            Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')->redirectAndExit(); 
                        }
                        else
                        {
                            $this->_request->setParam('privilege', $dPrivilege);
                        
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
    
    protected function allowKarmaAction($privilege)
    {
        if ($this->_auth->hasIdentity())
        {
            $identity = $this->_auth->getIdentity();
            if(intval($identity->karma) < $privilege->karma_needed)
                return false;
            else
                return true;
        }
        else
            return false;
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
        $json = Zend_Json::encode($body);

        // Prepare response
        $this->getResponse()
             ->setHttpResponseCode(200) // Or maybe HTTP Status 401 Unauthorized
             ->setBody($json)
             ->sendResponse();
    }
}


?>
