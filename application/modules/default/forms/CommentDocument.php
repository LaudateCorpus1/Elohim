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
class Default_Form_CommentDocument extends Zend_Form {

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
        $this->setAttrib('id', 'form_comment')
                ->setMethod('POST')
                ->setName('form_comment');

        $this->addElementsMessageForm();
    }

    public function addElementsMessageForm() {
        $this->text = new Zend_Form_Element_Textarea('form_comment_content');
        $this->text->setRequired(true)
                   ->setLabel("Commentaire")
                   ->setAttribs(array('rows' => '4', 'cols' => '50'))
                   ->addValidator('StringLength', false, array(3, 1500))
                   ->setDecorators($this->elementDecorators);

        $this->submit = new Zend_Form_Element_Submit('post_comment');
        $this->submit->setLabel('Envoyer')
                     ->setAttrib('class', 'btn')
                     ->setDecorators($this->buttonDecorators);

        $this->addElements(array($this->text, $this->submit));
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
