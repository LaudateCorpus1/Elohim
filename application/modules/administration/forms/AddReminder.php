<?php

class Administration_Form_AddReminder extends Zend_Form {
    
    public function init() {
        $this->setAttrib('id', 'form_reminder')
                ->setMethod('POST')
                ->setName('form_reminder');

        $this->addElementsForm();
    }

    public function addElementsForm() {
        $saveButton = new Zend_Form_Element_Submit('save');
        $saveButton->setLabel('Sauvegarder')
                      ->setAttrib('class', 'btn');
        
        $modelCategory = new Api_Model_Category();
        $categories = $modelCategory->getNamesFormFormatted();
        $categoriesSelect = new Zend_Form_Element_Select('reminder_category');
        $categoriesSelect->setRequired(true)
             ->setLabel('Catégorie')
             ->addMultiOptions($categories);
        
        $this->addElements(array(
            $this->createElement('text', 'reminder_title', array('size' => '88'))->setRequired(true)->setLabel('Titre'),
            $this->createElement('textarea', 'reminder_text', array('rows' => '7', 'cols' => '50'))->setRequired(true)->setLabel('Texte'),
            $categoriesSelect,
            $this->createElement('hash', get_class().'_csrf', array('salt' => 'unique', 'timeout' => 3600)),
            $saveButton
        ));
    }
}