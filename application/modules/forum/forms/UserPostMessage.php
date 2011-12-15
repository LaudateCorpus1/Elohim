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
class Forum_Form_UserPostMessage extends Zend_Form {

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
        $this->setAttrib('id', 'form_message')
                ->setMethod('POST')
                ->setName('form_message');

        $this->addElementsMessageForm();
    }

    public function addElementsMessageForm() {
        $this->text = new Zend_Form_Element_Textarea('form_message_content');
        $this->text->setRequired(true)
                   ->setLabel("Message")
                   ->setAttribs(array('rows' => '7', 'cols' => '50'))
                   ->setDecorators($this->elementDecorators);

        $this->submit = new Zend_Form_Element_Submit('post_message');
        $this->submit->setLabel('Envoyer')
                     ->setAttrib('class', 'btn primary')
                     ->setDecorators($this->buttonDecorators);
        
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
