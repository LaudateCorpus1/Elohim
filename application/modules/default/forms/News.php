<?php

class Default_Form_News extends Zend_Form {
    
    public function init() {
        $this->setAttrib('id', 'form_news')
                ->setMethod('POST')
                ->setName('form_news');

        $this->addElementsForm();
    }

    public function addElementsForm() {
        $this->addElements(array(
            $this->createElement('text', 'form_news_title', array('size' => '50'))->setRequired(true)->setLabel('Titre'),
            $this->createElement('textarea', 'form_news_content', array('rows' => '7', 'cols' => '50'))->setRequired(true)->setLabel("Contenu"),
            $this->createElement('submit', 'Envoyer', array('class' => 'btn')),
            $this->createElement('hash', get_class().'_csrf', array('salt' => 'unique'))
        ));
    }
}

?>
