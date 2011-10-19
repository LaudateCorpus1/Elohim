<?php

class Default_Form_Article extends Zend_Form {

    private $title;
    private $content;
    private $category;
    
    public function init() {
        $this->setAttrib('id', 'form_add_article')
                ->setMethod('POST')
                ->setName('form_add_article');

        $this->addElementsForm();
    }

    public function addElementsForm() {
        $this->title = new Zend_Form_Element_Text('title');
        $this->title->setLabel('Titre')->setRequired(true);
				      
        $this->content = new Zend_Form_Element_Textarea('content');
        $this->content->setLabel('Contenu') ->setRequired(true);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit	->setLabel('Envoyer');
        
        $categories_options = array('' => '---');
        $category_model = new Model_Category();
        $categories = $category_model->getAll();
        foreach($categories as $cat)
            $categories_options[$cat->id] = $cat->name;
        
        $this->category = new Zend_Form_Element_Select('category');
        $this->category->setLabel('Sélectionnez une catégorie')->setRequired(true);
        $this->category->addMultiOptions($categories_options);
                
        $this->addElement($this->title);
        $this->addElement($this->category);
        $this->addElement($this->content);
        $this->addElement($submit);
    }
    
    public function setDefaultValues($title = '', $content = '', $category_id = 0)
    {
        $this->title->setValue($title);
        $this->content->setValue($content);
        $this->category->setValue($category_id);
    }

}

?>
