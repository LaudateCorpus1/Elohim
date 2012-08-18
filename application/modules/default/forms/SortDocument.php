<?php

class Default_Form_SortDocument extends Zend_Form {

    public function init() {
        $this->setAttrib('id', 'form_sort_document')
                ->setMethod('POST')
                ->setName('form_sort_document');

        $this->addElementsForm();
    }

    public function addElementsForm() {
        
        // Code JS dans library.js
        $type = new Zend_Form_Element_Select('type');
        $type->setRequired(true)
             ->setLabel('Trier par ')
             ->addMultiOptions(array(
                        'date' => 'Date',
                        'votes' => 'Vote'
                    ))
             ->setDecorators(array(
                    'ViewHelper',
                    'Description',
                    'Errors',
                    array(array('data' => 'HtmlTag'), array('tag' => 'td')),
                    array('Label', array('tag' => 'td')),
                    array(array('row' => 'HtmlTag'), array('tag' => 'tr', 'openOnly' => true))
                ));
        
        $tag = new Zend_Form_Element_Hidden('tagname');
        $tag->setDecorators(array(
            'ViewHelper',
            'Description',
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr', 'closeOnly' => 'true'))
        ));
        
        $this->addElements(array(
            $type,
            $tag
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
