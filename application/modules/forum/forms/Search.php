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
class Forum_Form_Search extends Zend_Form {

    private $text;
    private $submit;

    private $elementDecorators = array(
        'ViewHelper',
        array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'element')),
        'Label',
        array(array('row' => 'HtmlTag'), array('tag' => 'li'))
    );
    
    private $buttonDecorators = array(
        'ViewHelper',
        array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'button')),
        array(array('row' => 'HtmlTag'), array('tag' => 'li')),
    );
    
    public function init() {
        $this->setAttrib('id', 'form_search_forum')
                ->setMethod('GET')
                ->setName('form_search_forum');

        $this->addElementsSearchForm();
    }

    public function addElementsSearchForm() {
        $this->text = new Zend_Form_Element_Text('form_search_content');
        $this->text->setRequired(true)
                   ->setLabel('Rechercher')
                   ->setAttrib('size', '18');
                   //->setDecorators($this->elementDecorators);
        
        $this->text->setDecorators(array(
            'ViewHelper',
            'Description',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr', 'openOnly' => true))
        ));

        $this->submit = new Zend_Form_Element_Submit('post_message');
        $this->submit->setLabel('Go');
                     //->setAttrib('class', 'btn primary')
                     //->setDecorators($this->buttonDecorators);
        
        $this->submit->setDecorators(array(
            'ViewHelper',
            'Description',
            'Errors', array(array('data' => 'HtmlTag'), array('tag' => 'td',
                    'align' => 'center')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr', 'closeOnly' => 'true'))
        ));
        
        $this->addElements(array($this->text, $this->submit));

//        $this->addElements(array(
//            $this->createElement('textarea', 'content', array('rows' => '7', 'cols' => '50', 'value' => $messageValue))->setRequired(true)->setLabel("Message"),
//            $this->createElement('submit', 'post')
//        ));
    }

    public function setDefaultMessage($message)
    {
        $this->text->setValue($message);
    }
    
    public function loadDefaultDecorators() {

        $this->setDecorators(array(
            'FormErrors',
            'FormElements',
            array('HtmlTag', array('tag' => 'ul')),
            'Form'
        ));
    }
}

?>
