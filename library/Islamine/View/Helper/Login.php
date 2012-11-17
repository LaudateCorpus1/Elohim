<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Islamine_View_Helper_FormCKEditor
 *
 * @author jeremie
 */
class Islamine_View_Helper_Login extends Zend_View_Helper_Abstract
{
    public function login(/*$value*/)
    {
        //$login = $this->view->placeholder('login');
        $url = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $user = Zend_Registry::get('user');
            $session = new Zend_Session_Namespace('islamine');
            
            $count = count($this->view->notification->toArray());
            $classNotif = 'user-top-notifications';
            if($count > 0)
                $classNotif = 'user-top-notifications new';
            
            if($count > 1)
                $title = ' notifications';
            else
                $title = ' notification';
            
            $session->redirect = $url;
            $html = '<div id="user-top-links-log">
                        <div class="user-top-info">
                            <div class="user-account-notif">
                                <div class="user-account-link">
                                    <a href="'.$this->view->url(array(
                                                                    'id' => $auth->getIdentity()->id,
                                                                    'username' => $auth->getIdentity()->login
                                    ), 'showUser', true).'"><i class="icon-user icon-white"></i> Mon compte</a>
                                </div>
                                <div class="'.$classNotif.'"><a class="notifications-link" title="'.$count.$title.'">'.$count.'</a></div>
                            </div>
                            <div>
                                <a href="'.$this->view->url(array(
                                                        'username' => $auth->getIdentity()->login
                                    ), 'userLibrary', true).'"><i class="icon-book icon-white"></i> Ma bibliothèque</a>
                            </div>
                        </div>
                        <div class="user-logout-link">
                            <div class="user-top-karma">'.$user->karma.' karma</div>
                            <a href="/default/user/logout"><i class="icon-off"></i> Se déconnecter</a>
                        </div>
                    </div>'.$this->buildNotificationsDiv($this->view->notification);
        }
        else
        {
            $forgotPasswordForm = new Default_Form_ForgotPassword();
            
            $login_form = new Default_Form_UserLoginTop();
            $login_form->populate(array('uri' => $url));
            $html = $login_form;
            
            $html .= '<div id="dialog-forgot-password" title="Mot de passe oublié" style="display:none;">
                <p>Vous recevrez un nouveau mot de passe par mail</p>
                '.$forgotPasswordForm.'
            </div>';
        }
        return $html;
    }
    
    private function buildNotificationsDiv($notifications)
    {
        $html = '<div class="notifications" style="display:none;"><a class="close-notifications">Fermer</a>';
        
        foreach($notifications as $notification)
        {
            $html .= '<div class="notification new">
                            '.$notification->message.' <a class="notification-id-'.$notification->id.'" href="'.$this->view->url(array(
                                                                    'document' => $notification->documentId,
                                                                    'title' => $notification->title
                                    ), 'showDocument').'#comments">'.$notification->title.'</a>
                      </div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
}


?>
