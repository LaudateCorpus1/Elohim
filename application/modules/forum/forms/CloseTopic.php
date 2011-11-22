<?php

class Forum_Form_CloseTopic extends Zend_Form {

    public function init() {
        $this->setAttrib('id', 'form_close_topic')
                ->setMethod('POST')
                ->setName('form_topic_close');

        $this->addElementsFormCloseTopic();
    }

    public function addElementsFormCloseTopic() {
        
        $this->addElements(array(
            $this->createElement('radio', 'close_motif')->setLabel('Motif')
                 ->addMultiOptions(array(
                        'Hors-sujet' => 'Hors-sujet',
                        'Sujet posté en deux fois' => 'Sujet posté en deux fois'
                    )),
            $this->createElement('hidden', 'topic_id'/*, array('disableLoadDefaultDecorators' => true)*/),
            $this->createElement('hidden', 'username'/*, array('disableLoadDefaultDecorators' => true)*/)
        ));
    }

}

?>
