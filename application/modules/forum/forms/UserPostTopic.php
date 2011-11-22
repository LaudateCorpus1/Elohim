<?php
require_once 'elements/Autocomplete.php';
/**
 * Description of Form_UserPostTopic
 *
 * @author jeremie
 */

class Forum_Form_UserPostTopic extends Zend_Form {

    public function init() {
        $this->setAttrib('id', 'form_topic')
                ->setMethod('POST')
                ->setName('form_topic');

        $this->addElementsTopicForm();
    }

    public function addElementsTopicForm() {

        $auto = new Forum_Form_Element_Autocomplete('tags');

//        $autoComplete = new ZendX_JQuery_Form_Element_AutoComplete('tags');
//        $autoComplete->setLabel('Tags')->setRequired(true);
//        $autoComplete->setJQueryParam('url', '/tag/autocomplete');
        $this->addElements(array(
            $this->createElement('text', 'title')->setRequired(true)->setLabel('Titre'),
            $this->createElement('textarea', 'content', array('rows' => '7', 'cols' => '50'))->setRequired(true)->setLabel('Message'),
//            $autoComplete,
            $this->createElement('text', 'tagsValues')->setRequired(true)->setLabel('Tags')->setDisableLoadDefaultDecorators(true),
            $auto,
            $this->createElement('submit', 'post')
        ));
    }

}

?>
