<?php

class Default_Form_SearchMosque extends Zend_Form {

    protected $text;
    
    public function setDefaultValue($value) {
        $this->text->setValue($value);
    }
    
    public function init() {
        $this->setAttrib('id', 'form_search_mosque')
                ->setAction('/mosque')
                ->setMethod('POST')
                ->setName('form_search_mosque');

        $this->addElementsSearchForm();
    }

    public function addElementsSearchForm() {
        $this->text = new Zend_Form_Element_Text('search_content', array('id' => 'geocomplete'));
        $this->text->setRequired(true)
                   ->setLabel('Chercher une mosquée')
                   ->setAttrib('class', 'input-lg')
                   ->setAttrib('size', '70');
        
        $this->text->setDecorators(array(
            'Errors',
            'ViewHelper',
            'Description',
            array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td', 'class' => 'label-large')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr', 'openOnly' => true))
        ));

        $submit = new Zend_Form_Element_Submit('find_mosque', array('id' => 'find'));
        $submit->setLabel('Trouver')
               ->setAttrib('class', 'btn-large');
        
        $submit->setDecorators(array(
            'ViewHelper',
            'Description',
            'Errors', 
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'align' => 'center')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr', 'closeOnly' => true))
        ));
        
        $this->addElements(array(
            $this->text, 
            $submit,
            
//            $this->createElement('hidden', 'formatted_address')->setDecorators(array('ViewHelper')),
            $this->createElement('hidden', 'route')->setDecorators(array('ViewHelper')),
            $this->createElement('hidden', 'street_number')->setDecorators(array('ViewHelper')),
//            $this->createElement('hidden', 'postal_code')->setDecorators(array('ViewHelper')),
            $this->createElement('hidden', 'locality')->setDecorators(array('ViewHelper')),
//            $this->createElement('hidden', 'sublocality')->setDecorators(array('ViewHelper')),
//            $this->createElement('hidden', 'administrative_area_level_1')->setDecorators(array('ViewHelper')),
//            $this->createElement('hidden', 'administrative_area_level_2')->setDecorators(array('ViewHelper')),
//            $this->createElement('hidden', 'administrative_area_level_3')->setDecorators(array('ViewHelper')),
            $this->createElement('hidden', 'country')->setDecorators(array('ViewHelper')),
            
        ));
    }
}

?>
