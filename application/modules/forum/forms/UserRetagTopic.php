<?php
require_once 'elements/Autocomplete.php';
/**
 * Description of Form_UserPostTopic
 *
 * @author jeremie
 */

class Forum_Form_UserRetagTopic extends Zend_Form {

    public function init() {
        $this->setAttrib('id', 'form_retag')
                ->setMethod('POST')
                ->setName('form_retag');

        $this->addElementsRetagForm();
    }

    public function addElementsRetagForm() {

        $auto = new Forum_Form_Element_Autocomplete('tags');
        
        $this->addElements(array(
            $this->createElement('text', 'tagsValues')->setRequired(true)->setLabel('Tags'),
            $auto,
            $this->createElement('submit', 'post')
        ));
    }

}

?>
