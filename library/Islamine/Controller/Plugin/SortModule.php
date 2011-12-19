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
        
       
        $view = Zend_Controller_Front::getInstance()
        ->getParam('bootstrap')
        ->getResource('view');
        
        $url = $view->url(array(
                'module' => 'forum',
                'controller' => $controller,
                'action' => 'sort', 
                't' => 'votes'));

        $default_list = '<ul>
                            <li><img src="/images/icone_vote.png" width="12" height="12" alt="sort-votes" /> <a href="'.$view->url(array(
                                                'module' => 'forum',
                                                'controller' => $controller,
                                                'action' => 'sort', 
                                                't' => 'votes')).'">Votes</a></li>
                                                    
                            <li><img src="/images/date.png" width="12" height="12" alt="sort-votes" /> <a href="'.$view->url(array(
                                                'module' => 'forum',
                                                'controller' => $controller,
                                                'action' => 'sort', 
                                                't' => 'date')).'">Date</a></li>
                                                    
                            <li><img src="/images/icone_answers.gif" width="12" height="12" alt="sort-votes" /> <a href="'.$view->url(array(
                                                'module' => 'forum',
                                                'controller' => $controller,
                                                'action' => 'sort', 
                                                't' => 'responses')).'">Réponses</a></li>
                                                    
                            <li><img src="/images/icone_activity.png" width="12" height="12" alt="sort-votes" /> <a href="'.$view->url(array(
                                                'module' => 'forum',
                                                'controller' => $controller,
                                                'action' => 'sort', 
                                                't' => 'activity')).'">Activité</a></li>';
        
        if(($controller == 'index' || $controller == 'topic') && $action == 'index')
        {
            $html .= 'Trier les sujets par'.$default_list.'
                        <li><img src="/images/icone_unanswered.png" width="12" height="12" alt="sort-votes" /> <a href="'.$view->url(array(
                                                'module' => 'forum',
                                                'controller' => $controller,
                                                'action' => 'sort', 
                                                't' => 'unanswered')).'">Sans réponse</a></li>';
            
            $auth = Zend_Auth::getInstance();
            if($auth->hasIdentity())
                $html .= '<li><img src="/images/icone_interesting.gif" width="12" height="12" alt="sort-votes" /> <a href="'.$view->url(array(
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
