<?php

class Default_Form_Contact extends Zend_Form {

    public function init() {
        $this->setAttrib('id', 'form_contact')
                ->setMethod('POST')
                ->setName('form_contcact');

        $this->addElementsForm();
    }

    public function addElementsForm() {
        $this->addElements(array(
            $this->createElement('text', 'form_contact_email', array('size' => '50'))->setRequired(true)->setLabel('Votre e-mail'),
            $this->createElement('text', 'form_contact_subject', array('size' => '50'))->setRequired(true)->setLabel('Sujet'),
            $this->createElement('textarea', 'form_contact_content', array('rows' => '7', 'cols' => '50'))->setRequired(true)->setLabel("Message"),
            $this->createElement('submit', 'Envoyer', array('class' => 'btn')),
            $this->createElement('hash', get_class().'_csrf', array('salt' => 'unique'))
        ));
    }

}

?>
