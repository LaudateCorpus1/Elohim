<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserPostMessage
 *
 * @author jeremie
 */
class Default_Form_UserLoginTop extends Zend_Form {

    public function init() {

        $this->setAttrib('id', 'form_login_top')
                ->setAction('/user/login')
                ->setMethod('POST')
                ->setName('form_login_top');

        $username = new Zend_Form_Element_Text('username', array(
                    'label' => 'Pseudo',
                    'required' => true,
                    'filters' => array(
                        'StringTrim'
                    ),
                    'validators' => array(
                        array('StringLength', false, array(3, 50))
                    ),
                    'class' => 'input-text'
                ));

        $username->setDecorators(array(
            'ViewHelper',
            'Description',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr', 'openOnly' => true))
        ));


        $password = new Zend_Form_Element_Password('password', array(
                    'label' => 'Mot de passe',
                    'required' => true,
                    'filters' => array(
                        'StringTrim'
                    ),
                    'validators' => array(
                        array('StringLength', false, array(5, 50))
                    ),
                    'class' => 'input-password'
                ));

        $password->setDecorators(array(
            'ViewHelper',
            'Description',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
        ));

        $URI = new Zend_Form_Element_Hidden('uri', array(
                    'required' => true
                ));
        
        $URI->setDecorators(array(
            'ViewHelper',
            'Description',
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td'))
        ));

        $submit = new Zend_Form_Element_Submit('login-btn', array(
                    'label' => 'Connexion',
                    'class' => 'btn btn-primary'
                ));

        $submit->setDecorators(array(
            'ViewHelper',
            'Description',
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td',
                    'colspan' => '2', 'align' => 'center')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr', 'closeOnly' => 'true'))
        ));
        
        $rememberMe = new Zend_Form_Element_Checkbox('rememberMe', array(
            'label' => 'Rester connecté',
            'required' => true,
            'description' => '<div id="user-top-links"><a href="/default/user/register">S\'inscrire</a> | <a href="">Mot de passe oublié</a></div>'
        ));
        
        $rememberMe->setDecorators(array(
            'ViewHelper',
            array('Description', array('escape' => false, 'tag' => false, 'placement' => 'prepend')),
            array(array('td' => 'HtmlTag'), array('tag' => 'td', 'colspan' => '5')),
            array('Label', array('tag' => 'td', 'placement' => 'append')),
            //array(array('row' => 'HtmlTag'), array('tag' => 'tr', 'closeOnly' => 'true'))
        ));

        $this->addElements(array(
            $username,
            $password,
            $URI,
            $submit,
            $rememberMe
        ));

        $this->setDecorators(array(
            'FormElements',
            array(array('data' => 'HtmlTag'), array('tag' => 'table')),
            'Form'
        ));
    }

}

?>
