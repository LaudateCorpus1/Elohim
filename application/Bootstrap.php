<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initAutoload() {
        
        $moduleLoader = new Zend_Application_Module_Autoloader(array(
                    'namespace' => '',
                    'basePath' => APPLICATION_PATH));
        
        return $moduleLoader;
    }
    
    protected function _initRoutes()
    {
        $frontController = Zend_Controller_Front::getInstance();
        $router = $frontController->getRouter();
        
        
        $router->addRoute('showTopic',
                          new Zend_Controller_Router_Route('/forum/topic/:topic',
                                            array('module' => 'forum',
                                                  'controller' => 'topic',
                                                  'action' => 'show'),
                                            array('topic' => '\d+'))); 
        /*$router->addRoute('indexTopic',
                          new Zend_Controller_Router_Route('/forum',
                                            array('module' => 'forum',
                                                  'controller' => 'index',
                                                  'action' => 'index'))); */
    }
    
    protected function _initTranslation()
    {
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
    
}

