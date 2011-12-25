<?php

class Islamine_Controller_Plugin_SortModule extends Zend_Controller_Plugin_Abstract {

    /**
     * preDispatch()
     *
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $html = '<div id="sort-module">%s</div>';
        
        $default_list = '
                        <li><img src="/images/icone_vote.png" width="12" height="12" alt="sort-votes" /> <a href="%s">Votes</a></li>

                        <li><img src="/images/date.png" width="12" height="12" alt="sort-date" /> <a href="%s">Date</a></li>

                        <li><img src="/images/icone_activity.png" width="12" height="12" alt="sort-activity" /> <a href="%s">Activité</a></li>';
        
        $controller = $request->getControllerName();
        if($controller == 'topic')
        {
            $topicId = $request->getParam('topic');
            $html = sprintf($html, $this->buildMessageHtml($default_list, $topicId));
        }
        else
            $html = sprintf($html, $this->buildTopicHtml($default_list));
                
        $layout = Zend_Layout::getMvcInstance();
        $layout->module_sort = $html;

    }
    
    protected function buildTopicHtml($default_list)
    {
        $view = Zend_Controller_Front::getInstance()
        ->getParam('bootstrap')
        ->getResource('view');
        
        $default_list = sprintf($default_list, 
                                $view->url(array('t' => 'votes'), 'sortTopic'),
                                $view->url(array('t' => 'date'), 'sortTopic'),
                                $view->url(array('t' => 'activity'), 'sortTopic')
                                );
        $return = 'Trier les sujets par
                    <ul>%s</ul>';
        
        $list = $default_list.'
                        <li><img src="/images/icone_unanswered.png" width="12" height="12" alt="sort-unanswered" /> <a href="'.$view->url(array(
                                                't' => 'unanswered'), 'sortTopic').'">Sans réponse</a></li>
                                                   
                        <li><img src="/images/icone_answers.gif" width="12" height="12" alt="sort-responses" /> <a href="'.$view->url(array(
                                                't' => 'responses'), 'sortTopic').'">Réponses</a></li>';
            
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
            $list .= '<li><img src="/images/icone_interesting.gif" width="12" height="12" alt="sort-interesting" /> <a href="'.$view->url(array(
                                                't' => 'interesting'), 'sortTopic').'">Intéressant</a></li>';
            
        $return = sprintf($return, $list);
        return $return;
    }
    
    protected function buildMessageHtml($default_list, $topicId)
    {
        $view = Zend_Controller_Front::getInstance()
        ->getParam('bootstrap')
        ->getResource('view');
        
        $default_list = sprintf($default_list, 
                                $view->url(array('topic' => $topicId, 't' => 'votes'), 'sortMessage'),
                                $view->url(array('topic' => $topicId, 't' => 'date'), 'sortMessage'),
                                $view->url(array('topic' => $topicId, 't' => 'activity'), 'sortMessage')
                                );
        
        $return = 'Trier les messages par
                    <ul>'.$default_list.'</ul>';
        
        return $return;
    }

}
