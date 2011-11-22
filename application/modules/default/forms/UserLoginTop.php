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
                    'label' => 'Username',
                    'required' => true,
                    'filters' => array(
                        'StringTrim'
                    ),
                    'validators' => array(
                        array('StringLength', false, array(3, 50))
                    ),
                    'class' => 'input-text'
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

            $URI = new Zend_Form_Element_Hidden('uri', array(
                    'required' => true
                ));
            $URI->setDecorators(array('ViewHelper'));

        /*$rememberMe = new Zend_Form_Element_Checkbox('rememberMe', array(
                    'decorators' => $this->checkboxDecorators,
                    'label' => 'Remember me?',
                    'required' => true,
                    'class' => 'input-checkbox'
                ));*/

        $submit = new Zend_Form_Element_Submit('login', array(
                    'label' => 'Login',
                    'class' => 'input-submit'
                ));
        
        $submit->removeDecorator('DtDdWrapper');


        $this->addElements(array(
            $username,
            $password,
            /*$rememberMe,*/
            $URI,
            $submit
        ));
    }
}

?>
