<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initAutoload() {
        
        $moduleLoader = new Zend_Application_Module_Autoloader(array(
                    'namespace' => '',
                    'basePath' => APPLICATION_PATH));
        
        return $moduleLoader;
    }
    
    protected function _initTranslation()
    {
        setlocale (LC_TIME, 'fr_FR.utf8','fra'); 
        $translator = new Zend_Translate(
          array(
              'adapter' => 'array',
              'content' => APPLICATION_PATH .'/resources/languages',
              'locale'  => 'fr',
              'scan' => Zend_Translate::LOCALE_DIRECTORY
          )
      );
      Zend_Validate_Abstract::setDefaultTranslator($translator);
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
        Zend_Controller_Action_HelperBroker::addHelper(new Islamine_Controller_Action_Helper_NotifyUser());
        Zend_Controller_Action_HelperBroker::addHelper(new Islamine_Controller_Action_Helper_UpdateTags());
        Zend_Controller_Action_HelperBroker::addHelper(new Islamine_Controller_Action_Helper_AlertMail());
        Zend_Controller_Action_HelperBroker::addHelper(new Islamine_Controller_Action_Helper_Vote());
    }

    protected function _initConstants()
    {
        $registry = Zend_Registry::getInstance();
        $registry->constants = new Zend_Config( $this->getApplication()->getOption('constants'));
    }
    
    protected function _initViewHelpers()
    {
        $view = new Zend_View($this->getOptions());
        $view->setEncoding('UTF-8');
        $view->headTitle()->setPostfix(' - Islamine');
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'ViewRenderer'
        );
        $viewRenderer->setView($view);
        return $view;
    }
    
    protected function _initSessions() 
    {
        Zend_Session::start();
    }
    
}

