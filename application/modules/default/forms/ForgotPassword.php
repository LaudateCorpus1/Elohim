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
class Default_Form_ForgotPassword extends Zend_Form 
{
    private $elementDecorators = array(
        'ViewHelper',
            array(array('td' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array('ErrorsHtmlTag', array('tag' => 'td', 'class' => 'help-error')),
            array(array('tr' => 'HtmlTag'), array('tag' => 'tr'))
            );
    
    public function init()
    {
        $this->addPrefixPath('Islamine_Form_Decorator', 'Islamine/Form/Decorator/', 'decorator');    
        
        $this->setAttrib('id', 'form_forgot_password')
                ->setMethod('POST')
                ->setName('form_forgot_password');

        $email = new Zend_Form_Element_Text('email', array(
            'label' => 'Votre e-mail',
            'required' => true,
            'filters' => array(
                'StringTrim'
            )
        ));
        
        $email->setDecorators($this->elementDecorators);
        
        $this->addElements(array(
            $email,
            $this->createElement('hash', get_class().'_csrf', array('salt' => 'unique'))->setDecorators($this->elementDecorators)
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
