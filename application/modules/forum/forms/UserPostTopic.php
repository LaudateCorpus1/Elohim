<?php
require_once 'elements/Autocomplete.php';
/**
 * Description of Form_UserPostTopic
 *
 * @author jeremie
 */

class Forum_Form_UserPostTopic extends Zend_Form {

    private $elementDecorators = array(
        'ViewHelper',
        array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'element')),
        array('Errors', array('class' => 'help-error')),
        'Label',
        array(array('row' => 'HtmlTag'), array('tag' => 'li'))
    );
    
    private $buttonDecorators = array(
        'ViewHelper',
        array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'button')),
        array(array('row' => 'HtmlTag'), array('tag' => 'li')),
    );
    
    public function init() {
        $this->setAttrib('id', 'form_topic')
                ->setMethod('POST')
                ->setName('form_topic');

        $this->addElementsTopicForm();
    }

    public function addElementsTopicForm() {

        $auto = new Forum_Form_Element_Autocomplete('tags');
        $auto->removeDecorator('Label');

//        $autoComplete = new ZendX_JQuery_Form_Element_AutoComplete('tags');
//        $autoComplete->setLabel('Tags')->setRequired(true);
//        $autoComplete->setJQueryParam('url', '/tag/autocomplete');
        $this->addElements(array(
            $this->createElement('text', 'form_topic_title', array('size' => '83'))->setRequired(true)->setLabel('Titre')->setDecorators($this->elementDecorators),
            $this->createElement('textarea', 'form_topic_content', array('rows' => '7', 'cols' => '50'))->setRequired(true)->setLabel('Message')->setDecorators($this->elementDecorators),
//            $autoComplete,
            $this->createElement('text', 'tagsValues')->setRequired(true)->setLabel('Mots clés')->setDecorators($this->elementDecorators)->addValidator(new Islamine_Validate_Tags()),
            $auto->setDecorators($this->elementDecorators),
            $this->createElement('submit', 'post')->setLabel('Envoyer')->setDecorators($this->buttonDecorators)->setAttrib('class', 'btn btn-primary')
        ));
    }
    
    public function loadDefaultDecorators() {

        $this->setDecorators(array(
            /*'FormErrors',*/
            'FormElements',
            array('HtmlTag', array('tag' => 'ul')),
            'Form'
        ));
    }

}

?>
