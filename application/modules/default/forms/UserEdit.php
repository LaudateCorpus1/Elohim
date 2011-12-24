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
class Default_Form_UserEdit extends Zend_Form {
    
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
        $this->setAttrib('enctype', 'multipart/form-data');
        
        $avatar = new Zend_Form_Element_File('avatar');
        $avatar->setLabel('Avatar');
                //->setDestination(APPLICATION_PATH.'/../data/users');
        // Fait en sorte qu'il y ait un seul fichier
        $avatar->addValidator('Count', false, 1);
        // limite à 100K
        $avatar->addValidator('Size', false, 204800);
        // seulement des JPEG, PNG, et GIFs
        $avatar->addValidator('Extension', false, 'jpg,png,gif');
        $avatar->setDecorators(array(
            'File',
            array(array('td' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array('ErrorsHtmlTag', array('tag' => 'td', 'class' => 'help-error')),
            array(array('tr' => 'HtmlTag'), array('tag' => 'tr'))
            ));

        $firstName = new Zend_Form_Element_Text('first_name', array(
                    'label' => 'Prénom',
                    'filters' => array(
                        'StringTrim'
                    ),
                    'validators' => array(
                        array('StringLength', false, array(2, 50))
                    )
                ));
        
        $firstName->setDecorators(array(
            'ViewHelper',
            array(array('td' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array('ErrorsHtmlTag', array('tag' => 'td', 'class' => 'help-error')),
            array(array('tr' => 'HtmlTag'), array('tag' => 'tr'))
            ));

        $lastName = new Zend_Form_Element_Text('last_name', array(
                    'label' => 'Nom',
                    'filters' => array(
                        'StringTrim'
                    ),
                    'validators' => array(
                        array('StringLength', false, array(2, 50))
                    )
                ));
        
        $lastName->setDecorators(array(
            'ViewHelper',
            array(array('td' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array('ErrorsHtmlTag', array('tag' => 'td', 'class' => 'help-error')),
            array(array('tr' => 'HtmlTag'), array('tag' => 'tr'))
            ));
        
        $location = new Zend_Form_Element_Text('location', array(
                    'label' => 'Lieu',
                    'filters' => array(
                        'StringTrim'
                    ),
                    'validators' => array(
                        array('StringLength', false, array(2, 50))
                    )
                ));
        
        $location->setDecorators(array(
            'ViewHelper',
            array(array('td' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array('ErrorsHtmlTag', array('tag' => 'td', 'class' => 'help-error')),
            array(array('tr' => 'HtmlTag'), array('tag' => 'tr'))
            ));

        $email = new Zend_Form_Element_Text('email', array(
                    'label' => 'E-mail',
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
        
        $email_validator = new Zend_Validate_Db_NoRecordExists('user', 'email',
                                                    array('field' => 'id','value' => Zend_Auth::getInstance()->getIdentity()->id)
                                                    );
        $email->addValidator($email_validator);
        
        $oldPassword = new Zend_Form_Element_Password('oldPassword', array(
                    'label' => 'Ancien mot de passe',
                    'filters' => array(
                        'StringTrim'
                    ),
                    'validators' => array(
                        array('StringLength', false, array(6, 50))
                    )
                ));
        
        $oldPassword->setDecorators(array(
            'ViewHelper',
            array(array('td' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array('ErrorsHtmlTag', array('tag' => 'td', 'class' => 'help-error')),
            array(array('tr' => 'HtmlTag'), array('tag' => 'tr'))
            ));

        $password = new Zend_Form_Element_Password('password', array(
                    'label' => 'Nouveau mot de passe',
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
                    'label' => 'Confirmation nouveau mot de passe',
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
        
        $submit = new Zend_Form_Element_Submit('editbtn', array(
                    'decorators' => $this->buttonDecorators,
                    'label' => 'Valider',
                    'class' => 'btn primary'
                ));



        $this->addElements(array(
            $avatar,
            $firstName,
            $lastName,
            $location,
            $email,
            $oldPassword,
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
