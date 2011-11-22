<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initAutoload() {
        
        $moduleLoader = new Zend_Application_Module_Autoloader(array(
                    'namespace' => '',
                    'basePath' => APPLICATION_PATH));
        
        return $moduleLoader;
    }
    
    protected function _initAcl()
    {
        $resource = $this->getPluginResource('db');
        $db = $resource->getDbAdapter();

        $acl_ini = APPLICATION_PATH . '/configs/acl.ini';
        $acl = new Islamine_Acl($acl_ini, $db);
        Zend_Registry::set('acl', $acl);
    }
    
    protected function _initActionHelpers ()
    {
        Zend_Controller_Action_HelperBroker::addHelper(new Islamine_Controller_Action_Helper_HasAccess());
    }


}

