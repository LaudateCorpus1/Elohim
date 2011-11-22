<?php

class Islamine_Controller_Plugin_SortModule extends Zend_Controller_Plugin_Abstract {

    /**
     * preDispatch()
     *
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        
        $html = '<div id="sort-module">';
        
        /*$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        if (null === $viewRenderer->view) {
            $viewRenderer->initView();
        }
        $view = $viewRenderer->view;*/
        
        $view = Zend_Controller_Front::getInstance()
        ->getParam('bootstrap')
        ->getResource('view');
        
        $url = $view->url(array(
                'module' => 'forum',
                'controller' => $controller,
                'action' => 'sort', 
                't' => 'votes'));

        $default_list = '<ul>
                            <li><a href="'.$view->url(array(
                                                'module' => 'forum',
                                                'controller' => $controller,
                                                'action' => 'sort', 
                                                't' => 'votes')).'">Votes</a></li>
                                                    
                            <li><a href="'.$view->url(array(
                                                'module' => 'forum',
                                                'controller' => $controller,
                                                'action' => 'sort', 
                                                't' => 'date')).'">Date</a></li>
                                                    
                            <li><a href="'.$view->url(array(
                                                'module' => 'forum',
                                                'controller' => $controller,
                                                'action' => 'sort', 
                                                't' => 'responses')).'">Réponses</a></li>
                                                    
                            <li><a href="'.$view->url(array(
                                                'module' => 'forum',
                                                'controller' => $controller,
                                                'action' => 'sort', 
                                                't' => 'activity')).'">Activité</a></li>';
        
        if(($controller == 'index' || $controller == 'topic') && $action == 'index')
        {
            $html .= 'Trier les sujets par'.$default_list.'
                        <li><a href="'.$view->url(array(
                                                'module' => 'forum',
                                                'controller' => $controller,
                                                'action' => 'sort', 
                                                't' => 'unanswered')).'">Sans réponse</a></li>';
            
            $auth = Zend_Auth::getInstance();
            if($auth->hasIdentity())
                $html .= '<li><a href="'.$view->url(array(
                                                'module' => 'forum',
                                                'controller' => $controller,
                                                'action' => 'sort', 
                                                't' => 'interesting')).'">Intéressant</a></li>';
        
        }
        else
            $html .= 'Trier les messages par'.$default_list;
            
        $html .= '</ul>
                </div>';
        
        $layout = Zend_Layout::getMvcInstance();
        $layout->module_sort = $html;

    }

}
