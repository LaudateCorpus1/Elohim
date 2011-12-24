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
                          new Zend_Controller_Router_Route('/forum/:topic/:title',
                                            array('module' => 'forum',
                                                  'controller' => 'topic',
                                                  'action' => 'show',
                                                  'topic' => '1',
                                                  'title' => ''),
                                            array('topic' => '\d+'))); 
        
        $router->addRoute('indexForum',
                          new Zend_Controller_Router_Route('/forum',
                                            array('module' => 'forum',
                                                  'controller' => 'index',
                                                  'action' => 'index')));
        
        $router->addRoute('showUser',
                          new Zend_Controller_Router_Route('/users/:id/:username',
                                            array('module' => 'default',
                                                  'controller' => 'user',
                                                  'action' => 'index',
                                                  'id' => '1',
                                                  'username' => ''),
                                            array('id' => '\d+'))); 
        
        /*$router->addRoute('showTopic',
                          new Zend_Controller_Router_Route_Regex('forum/topic/(/d+)',
                                            array('module' => 'forum',
                                                  'controller' => 'topic',
                                                  'action' => 'show')));*/
        
        //Zend_Debug::dump($router); exit;

        
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

