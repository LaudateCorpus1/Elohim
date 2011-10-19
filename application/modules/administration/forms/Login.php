<?php

class Administration_Form_Login extends Zend_Form {

    public function init() {
        $this->setAttrib('id', 'form_admin_login')
                ->setMethod('POST')
                ->setName('form_admin_login');

        $this->addElementsForm();
    }

    public function addElementsForm() {
        $login = new Zend_Form_Element_Text('login');
		$login	->setLabel('Identifiant')
				->setRequired(TRUE)
				->addFilters(array('StringTrim', 'StripTags'))
				->addValidators(array(	array('validator' => 'StringLength', 'options' => array(0, 20))))
                ->getDecorator('label')->setOption('tag', null);

                
        $passwd = new Zend_Form_Element_Password('password');
        $passwd	->setLabel('Mot de Passe')
        		->setRequired(TRUE)
        		->addFilters(array('StringTrim', 'StripTags'))
        		->addValidators(array(	array('validator' => 'StringLength', 'options' => array(0, 20))))
                ->getDecorator('label')->setOption('tag', null);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit	->setLabel('Connexion')
        		->setIgnore(true);
                
        $this->addElement($login);
        $this->addElement($passwd);
        $this->addElement($submit);
        
        /*$this->setDecorators(array(
			    array('ViewScript', array('viewScript' => 'login.phtml'))
			));*/
    }

}

?>
