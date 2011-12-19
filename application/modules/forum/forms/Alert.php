<?php

class Forum_Form_Alert extends Zend_Form {

    public function init() {
        $this->setAttrib('id', 'form_alert')
                ->setMethod('POST')
                ->setName('form_alert');

        $this->addElementsAlert();
    }

    public function addElementsAlert() {
        
        $this->addElements(array(
            $this->createElement('select', 'motif')->setRequired(true)->setLabel('Motif')
                 ->addMultiOptions(array(
                        '' => '---------',
                        'Insulte' => 'Insulte',
                        'Racisme/Incitaton à la haine' => 'Racisme/Incitaton à la haine',
                        'Pornographie' => 'Pornographie',
                        'Flood' => 'Flood',
                        'Propos non appropriés' => 'Propos non appropriés',
                        'Autre' => 'Autre'
                    )),
            $this->createElement('submit', 'Valider')
        ));
    }

}

?>
