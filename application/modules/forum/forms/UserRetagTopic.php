<?php
require_once 'elements/Autocomplete.php';
/**
 * Description of Form_UserPostTopic
 *
 * @author jeremie
 */

class Forum_Form_UserRetagTopic extends Zend_Form {

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
        $this->setAttrib('id', 'form_retag')
                ->setMethod('POST')
                ->setName('form_retag');

        $this->addElementsRetagForm();
    }

    public function addElementsRetagForm() {

        $auto = new Forum_Form_Element_Autocomplete('tags');
        $auto->removeDecorator('Label');
        
        $this->addElements(array(
            $this->createElement('text', 'tagsValues')->setRequired(true)->setLabel('Mots clés')->setDecorators($this->elementDecorators)->addValidator(new Islamine_Validate_Tags()),
            $auto->setDecorators($this->elementDecorators),
            $this->createElement('submit', 'post')->setLabel('Envoyer')->setDecorators($this->buttonDecorators)->setAttrib('class', 'btn btn-primary')
        ));
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
