<?php

class Default_Form_DocumentAlert extends Zend_Form {

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
        $this->setAttrib('id', 'form_document_alert')
                ->setMethod('POST')
                ->setName('form_document_alert');

        $this->addElementsAlert();
    }

    public function addElementsAlert() {
        
        $this->addElements(array(
            $this->createElement('text', 'motif', array('size' => '60'))->setRequired(true)->setLabel('Motif')->setDecorators($this->elementDecorators),
            $this->createElement('submit', 'validate_document_alert')->setLabel('Valider')->setAttrib('class', 'btn btn-primary')->setDecorators($this->buttonDecorators)
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
