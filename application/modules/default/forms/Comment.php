<?php

class Default_Form_Comment extends Zend_Form {

    public function init() {
        $this->setAttrib('id', 'form_comment')
                ->setMethod('POST')
                ->setName('form_comment');

        $this->addElementsForm();
    }

    public function addElementsForm() {
        $this->addElements(array(
            $this->createElement('text', 'pseudo')->setRequired(true)->setLabel('Pseudo'),
            $this->createElement('textarea', 'contentcomment', array('rows' => '7', 'cols' => '50'))->setRequired(true)->setLabel("Message"),
            $this->createElement('hash', get_class().'_csrf', array('salt' => 'unique')),
            $this->createElement('submit', 'Envoyer')
        ));
    }

}

?>
