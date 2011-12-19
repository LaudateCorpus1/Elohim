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
class Default_Form_UserLogin extends Zend_Form 
{
    private $buttonDecorators = array(
        'ViewHelper',
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
        array(array('label' => 'HtmlTag'), array('tag' => 'td', 'placement' => 'prepend')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
    );

    public function init()
    {
        $this->addPrefixPath('Islamine_Form_Decorator', 'Islamine/Form/Decorator/', 'decorator');    
        
        $this->setMethod('post');

        $username = new Zend_Form_Element_Text('username', array(
            'label' => 'Pseudo',
            'required' => true,
            'filters' => array(
                'StringTrim'
            )/*,
            'validators' => array(
                array('StringLength', false, array(3, 50))
            )*/
        ));
        
        $username->setDecorators(array(
            'ViewHelper',
            array(array('td' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array('ErrorsHtmlTag', array('tag' => 'td', 'class' => 'help-error')),
            array(array('tr' => 'HtmlTag'), array('tag' => 'tr'))
            ));
        
        //$username->addValidator('NotEmpty', true)->addErrorMessage('Value is empty, but a non-empty value is required.');
        
        $password = new Zend_Form_Element_Password('password', array(
            'label' => 'Mot de passe',
            'required' => true,
            'filters' => array(
                'StringTrim'
            )/*,
            'validators' => array(
                array('StringLength', false, array(5, 50))
            )*/
        ));
        
        $password->setDecorators(array(
            'ViewHelper',
            array(array('td' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array('ErrorsHtmlTag', array('tag' => 'td', 'class' => 'help-error')),
            array(array('tr' => 'HtmlTag'), array('tag' => 'tr'))
        ));

        $rememberMe = new Zend_Form_Element_Checkbox('rememberMe', array(
            'label' => 'Rester connecté',
            'required' => true
        ));
        
        $rememberMe->setDecorators(array(
            'ViewHelper',
            array(array('td' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array('ErrorsHtmlTag', array('tag' => 'td', 'class' => 'help-error')),
            array(array('tr' => 'HtmlTag'), array('tag' => 'tr'))
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
            'decorators' => $this->buttonDecorators,
            'label' => 'Connexion',
            'class' => 'btn primary'
        ));
  
        $this->addElements(array(
            $username,
            $password,
            $rememberMe,
            $URI,
            $submit
        ));
    }

    public function loadDefaultDecorators()
    {
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form',
        ));
    }
}

?>
