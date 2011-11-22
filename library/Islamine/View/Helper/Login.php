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
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $html = '<div id="user-top-links-log"><a href="">Mon compte</a> <br /> <a href="/default/user/logout">Se déconnecter</a></div>';
        }
        else
        {
            $login_form = new Default_Form_UserLoginTop();
            $request = Zend_Controller_Front::getInstance()->getRequest();
            $login_form->populate(array('uri' => $request->getRequestUri()));
            $html = $login_form.'<div id="user-top-links"><a href="/default/user/register">S\'inscrire</a> | <a href="">Mot de passe oublié</a></div>';
            
            //$formData = $request->getParams();
            //if ($login_form->isValid($formData))
            {
                //
                //$this->_forward('default', 'user', 'login');
            }
        }
        
        
        /*if($this->getRequest()->isPost()) 
        {
            $this->_forward('default', 'user', 'login');
        }*/
        
        //$login->append($html);
        return $html;
    }
}


?>
