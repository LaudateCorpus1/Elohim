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
    
private $elementDecorators = array(

        'ViewHelper',

        array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'element')),

        'Label',

        array(array('row' => 'HtmlTag'), array('tag' => 'li')),

    );

 

    private $buttonDecorators = array(

        'ViewHelper',

        array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'button')),

        array(array('row' => 'HtmlTag'), array('tag' => 'li')),

    );

 

    private $checkboxDecorators = array(

        'Label',

        'ViewHelper',

        array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'checkbox')),

        array(array('row' => 'HtmlTag'), array('tag' => 'li')),

    );

 

    public function init()

    {

        $this->setMethod('post');

 

        $username = new Zend_Form_Element_Text('username', array(

            'decorators' => $this->elementDecorators,

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

            'decorators' => $this->elementDecorators,

            'label' => 'Password',

            'required' => true,

            'filters' => array(

                'StringTrim'

            ),

            'validators' => array(

                array('StringLength', false, array(5, 50))

            ),

            'class' => 'input-password'

        ));

 

        $rememberMe = new Zend_Form_Element_Checkbox('rememberMe', array(

            'decorators' => $this->checkboxDecorators,

            'label' => 'Remember me?',

            'required' => true,

            'class' => 'input-checkbox'

        ));

 

        $submit = new Zend_Form_Element_Submit('login', array(

            'decorators' => $this->buttonDecorators,

            'label' => 'Login',

            'class' => 'input-submit'

        ));

 

        $this->addElements(array(

            $username,

            $password,

            $rememberMe,

            $submit

        ));

    }

 

    public function loadDefaultDecorators()

    {

        $this->setDecorators(array(

            'FormErrors',

            'FormElements',

            array('HtmlTag', array('tag' => 'ol')),

            'Form'

        ));
    }

}

?>
