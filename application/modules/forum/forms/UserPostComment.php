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
class Forum_Form_UserPostComment extends Zend_Form {

    private $text;
    private $submit;

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
                   ->setAttribs(array('rows' => '4', 'cols' => '50'));

        $this->submit = new Zend_Form_Element_Submit('post_comment');
        $this->submit->setLabel('Envoyer');

        $this->addElements(array($this->text, $this->submit));
    }

    public function setDefaultMessage($message)
    {
        $this->text->setValue($message);
    }

}

?>
