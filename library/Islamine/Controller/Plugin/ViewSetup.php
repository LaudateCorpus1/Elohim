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

class Islamine_Controller_Plugin_ViewSetup extends Zend_Controller_Plugin_Abstract	
{
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $viewRenderer->init();
        
        $viewRenderer->view->module = $request->getModuleName();
        $viewRenderer->view->controller = $request->getControllerName();
        $viewRenderer->view->action = $request->getActionName();
    } 
}


?>
