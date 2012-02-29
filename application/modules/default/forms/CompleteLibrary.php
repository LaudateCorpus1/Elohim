<?php
require_once APPLICATION_PATH.'/modules/forum/forms/elements/Autocomplete.php';

class Default_Form_CompleteLibrary extends Zend_Form {

    private $elementDecorators = array(
        'ViewHelper',
        array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'element')),
        array('Errors', array('class' => 'help-error')),
        'Label',
        array(array('row' => 'HtmlTag'), array('tag' => 'li'))
    );
    
    private $buttonDecorators = array(
        'ViewHelper',
        array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'button')),
        array(array('row' => 'HtmlTag'), array('tag' => 'li')),
    );
    
    public function init() {
        $this->setAttrib('id', 'form_complete_library')
                ->setMethod('POST')
                ->setName('form_complete_library');

        $this->addElementsForm();
    }

    public function addElementsForm() {
        $auto = new Forum_Form_Element_Autocomplete('tags');
        $auto->removeDecorator('Label');
        
        $this->addElements(array(
            $this->createElement('text', 'form_document_library_header', array('size' => '83'))->setRequired(true)->setLabel('Titre / Adresse d\'un site')->setDecorators($this->elementDecorators),
            $this->createElement('textarea', 'form_document_library_description', array('rows' => '7', 'cols' => '50'))->setRequired(true)->setLabel('Texte / Description de l\'adresse')->setDecorators($this->elementDecorators),
            $this->createElement('text', 'tagsValues')->setRequired(true)->setLabel('Mots-clés')->setDecorators($this->elementDecorators)->addValidator(new Islamine_Validate_Tags()),
            $auto->setDecorators($this->elementDecorators),
            $this->createElement('submit', 'post')->setLabel('Envoyer')->setDecorators($this->buttonDecorators)->setAttrib('class', 'btn btn-primary')
        ));
    }
    
    public function loadDefaultDecorators() {

        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'ul')),
            'Form'
        ));
    }

}

?>
