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
            
            $classNotif = 'user-top-notifications';
            if($this->view->notification != '0')
                $classNotif = 'user-top-notifications new';
            
            $session->redirect = $url;
            $html = '<div id="user-top-links-log">
                        <div class="user-top-info">
                            <div class="user-account-notif">
                                <div class="user-account-link"><a href="">Mon compte</a></div>
                                <div class="'.$classNotif.'"><a>'.$this->view->notification.'</a></div>
                            </div>
                            <div class="user-top-karma">'.$user->karma.' karma</div>
                        </div>
                        <div class="user-logout-link">
                            <a href="/default/user/logout">Se déconnecter</a>
                        </div>
                    </div>';
        }
        else
        {
            $login_form = new Default_Form_UserLoginTop();
            $login_form->populate(array('uri' => $url));
            $html = $login_form;

        }
        return $html;
    }
    
    private function buildNotificationsDiv()
    {
        
    }
}


?>
