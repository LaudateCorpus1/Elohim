<?php

class Forum_Form_ReopenTopic extends Zend_Form {

    public function init() {
        $this->setAttrib('id', 'form_reopen_topic')
                ->setMethod('POST')
                ->setName('form_reopen_topic');

        $this->addElementsFormReopenTopic();
    }

    public function addElementsFormReopenTopic() {
        
        $this->addElements(array(
            $this->createElement('submit', 'Valider')
        ));
    }

}

?>
