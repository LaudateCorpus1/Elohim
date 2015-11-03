<?php

require_once 'elements/Autocomplete.php';

class Administration_Form_AddSahabaStory extends Zend_Form {
    
    public function init() {
        $this->setAttrib('id', 'form_sahaba_story')
                ->setMethod('POST')
                ->setName('form_sahaba_story');

        $this->addElementsForm();
    }

    public function addElementsForm() {
        $auto = new Administration_Form_Element_Autocomplete('tags');
        $auto->removeDecorator('Label');
                        
        $saveButton = new Zend_Form_Element_Submit('save');
        $saveButton->setLabel('Sauvegarder')
                      ->setAttrib('class', 'btn');
        
        $this->addElements(array(
            $this->createElement('text', 'sahaba_story_source', array('size' => '88'))->setRequired(true)->setLabel('Source'),
            $this->createElement('textarea', 'sahaba_story', array('rows' => '7', 'cols' => '50'))->setRequired(true)->setLabel('Texte'),
            $this->createElement('textarea', 'sahaba_comment', array('rows' => '5', 'cols' => '50'))->setLabel('Commentaire'),
            $this->createElement('text', 'sahabas_values')->setRequired(true)->setLabel('Pieux prédécesseurs'),
            $auto,
            $this->createElement('hash', get_class().'_csrf', array('salt' => 'unique', 'timeout' => 3600)),
            $saveButton
        ));
    }
}