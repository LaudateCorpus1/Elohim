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
class Default_Form_UserRegister extends Zend_Form {
    
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

        $email = new Zend_Form_Element_Text('email', array(
                    'label' => 'E-mail',
                    'required' => true,
                    'filters' => array(
                        'StringTrim'
                    ),
                    'validators' => array(
                        'EmailAddress'
                    )
                ));
        
        $email->setDecorators(array(
            'ViewHelper',
            array(array('td' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array('ErrorsHtmlTag', array('tag' => 'td', 'class' => 'help-error')),
            array(array('tr' => 'HtmlTag'), array('tag' => 'tr'))
            ));
        
        $email_validator = new Zend_Validate_Db_NoRecordExists(array(
                                                    'table' => 'user',
                                                    'field' => 'email'
                                                    ));
        $email->addValidator($email_validator);

        $username = new Zend_Form_Element_Text('username', array(
                    'label' => 'Pseudo',
                    'required' => true,
                    'filters' => array(
                        'StringTrim'
                    ),
                    'validators' => array(
                        array('StringLength', false, array(3, 50))
                    )
                ));
        
        $username->setDecorators(array(
            'ViewHelper',
            array(array('td' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array('ErrorsHtmlTag', array('tag' => 'td', 'class' => 'help-error')),
            array(array('tr' => 'HtmlTag'), array('tag' => 'tr'))
            ));
        
        $validator = new Zend_Validate_Db_NoRecordExists(array(
                                                    'table' => 'user',
                                                    'field' => 'login'
                                                    ));
        $username->addValidator($validator);

        $password = new Zend_Form_Element_Password('password', array(
                    'label' => 'Mot de passe',
                    'required' => true,
                    'description' => '6 caractères minimum',
                    'filters' => array(
                        'StringTrim'
                    ),
                    'validators' => array(
                        array('StringLength', false, array(6, 50))
                    )
                ));
        
        $password->setDecorators(array(
            'ViewHelper',
            array(array('td' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array('ErrorsHtmlTag', array('tag' => 'td', 'class' => 'help-error')),
            array(array('tr' => 'HtmlTag'), array('tag' => 'tr')),
            array('Description', array('tag' => 'td')),
            ));
        
        $passwordAgain = new Zend_Form_Element_Password('passwordAgain', array(
                    'label' => 'Confirmation mot de passe',
                    'required' => true,
                    'filters' => array(
                        'StringTrim'
                    ),
                    'validators' => array(
                        array('StringLength', false, array(6, 50))
                    )
                ));
        
        $passwordAgain->setDecorators(array(
            'ViewHelper',
            array(array('td' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array('ErrorsHtmlTag', array('tag' => 'td', 'class' => 'help-error')),
            array(array('tr' => 'HtmlTag'), array('tag' => 'tr'))
            ));
        
        $submit = new Zend_Form_Element_Submit('registerbtn', array(
                    'decorators' => $this->buttonDecorators,
                    'label' => 'S\'inscrire',
                    'class' => 'btn primary'
                ));



        $this->addElements(array(
            $email,
            $username,
            $password,
            $passwordAgain,
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
