<?php

class Default_Form_MosqueCreate extends Zend_Form {

    private $elementDecorators = array(
        'ViewHelper',
        array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'element')),
        array('Errors', array('class' => 'help-error')),
        array('Label', array('requiredSuffix' => ' *', 'optionalSuffix' => ' (optionnel)')),
        array(array('row' => 'HtmlTag'), array('tag' => 'li'))
    );
    
    private $radioDecorators = array(
        'ViewHelper',
        array(array('data' => 'HtmlTag'), array('tag' => 'div')),
        array('Errors', array('class' => 'help-error')),
        array('Label', array('requiredSuffix' => ' *', 'optionalSuffix' => ' (optionnel)')),
        array(array('row' => 'HtmlTag'), array('tag' => 'li', 'class' => 'radio-btn'))
    );
    
    private $checkboxDecorators = array(
        'ViewHelper',
        array(array('data' => 'HtmlTag'), array('tag' => 'div')),
        array('Errors', array('class' => 'help-error')),
        array('Label', array('class' => 'label-left')),
        array(array('row' => 'HtmlTag'), array('tag' => 'li', 'class' => 'chkbx'))
    );
    
    private $buttonDecorators = array(
        'ViewHelper',
        array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'button')),
        array(array('row' => 'HtmlTag'), array('tag' => 'li')),
    );
    
    public function init() {
        $this->addPrefixPath('Islamine_Form_Decorator', 'Islamine/Form/Decorator/', 'decorator'); 
        
        $this->setAttrib('id', 'form_create_mosque')
                ->setMethod('POST')
                ->setName('form_create_mosque');

        $this->addElementsForm();
    }

    public function addElementsForm() {
        
        $saveButton = new Zend_Form_Element_Submit('create');
        $saveButton->setLabel('Ajouter')
                      ->setDecorators($this->buttonDecorators)
                      ->setAttrib('class', 'btn btn-large btn-primary');
        
        $categoriesSelect = new Zend_Form_Element_Select('mosque_type');
        $categoriesSelect->setRequired(true)
             ->setLabel('Type')
             ->addMultiOptions(array(
                    '' => '---',
                    'mosque' => 'Mosquée',
                    'prayer_room' => 'Salle de prière'
                 ))
             ->setDecorators($this->elementDecorators);   
        
        $yesNoArray = array(
            '1' => 'Oui',
            '0' => 'Non',
        );
        
        $uniqueValidator = new Zend_Validate_Db_NoRecordExists(array(
                                                    'table' => 'address',
                                                    'field' => 'formatted'
                                                    ));
        
        $sameValidator = new Zend_Validate_Identical('form_mosque_address');
        
        $captcha = new Zend_Form_Element_Captcha('captcha-mosque', array(
            'label' => 'Entrez le texte',
            'captcha' => array(
                'captcha' => 'Image',
                'wordLen' => 4,
                // Niveau de bruit verticale (par défaut 100)
                'dotNoiseLevel' => 77,
                // Niveau de bruit horizontale (par défaut 5)
                'lineNoiseLevel' => 5,
                'width' => 150,
                'font' => APPLICATION_PATH.'/../public/fonts/Verdana.ttf',
                'imgDir'    => APPLICATION_PATH.'/../public/images/captcha',
                'timeout'   => 120,
                'expiration'=> 300
             )
        ));
        
        $captcha->setDecorators(array(
            array(array('td' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'td')),
            array('ErrorsHtmlTag', array('tag' => 'td', 'class' => 'help-error')),
            array(array('tr' => 'HtmlTag'), array('tag' => 'tr')),
            ));
        
        $div = $this->createElement(
            'hidden',
            'existing-mosques',
            array(
                'required' => false,
                'ignore' => true,
                'autoInsertNotEmptyValidator' => false,
                'decorators' => array(
                    array(
                        'HtmlTag', array(
                            'tag'  => 'div',
                            'class' => 'existing-mosques'
                        )
                    )
                )
        ));
      
        $this->addElements(array(
            $this->createElement('text', 'mosque_name', array('size' => '60'))->setLabel('Nom')->setRequired(true)->setDecorators($this->elementDecorators),
            $categoriesSelect,
            $this->createElement('text', 'form_mosque_address', array('id' => 'geocomplete', 'size' => '60'))->setRequired(true)->setLabel('Entrer une adresse')->setDecorators($this->elementDecorators),
            $div->clearValidators(),
            $this->createElement('hidden', 'formatted_address')->setDecorators($this->elementDecorators)->addValidator($uniqueValidator),
//            $this->createElement('hidden', 'route')->setDecorators($this->elementDecorators),
//            $this->createElement('hidden', 'street_number')->setDecorators($this->elementDecorators),
//            $this->createElement('hidden', 'postal_code')->setDecorators($this->elementDecorators),
            $this->createElement('hidden', 'locality')->setDecorators($this->elementDecorators),
//            $this->createElement('hidden', 'sublocality')->setDecorators($this->elementDecorators),
            $this->createElement('hidden', 'country')->setDecorators($this->elementDecorators),
//            $this->createElement('hidden', 'administrative_area_level_1')->setDecorators($this->elementDecorators),
//            $this->createElement('hidden', 'administrative_area_level_2')->setDecorators($this->elementDecorators),
//            $this->createElement('hidden', 'administrative_area_level_3')->setDecorators($this->elementDecorators),
//            $this->createElement('hidden', 'lat')->setDecorators($this->elementDecorators),
//            $this->createElement('hidden', 'lng')->setDecorators($this->elementDecorators),
//            $this->createElement('hidden', 'website')->setDecorators($this->elementDecorators),
            $this->createElement('text', 'mosqueWebsite', array('size' => '60'))->setLabel('Site internet')->setDecorators($this->elementDecorators),
            $this->createElement('text', 'nbMenRooms', array('size' => '3'))->setLabel('Nb salles hommes')->setDecorators($this->elementDecorators)->addValidator('Digits'),
            $this->createElement('text', 'nbWomenRooms', array('size' => '3'))->setLabel('Nb salles femmes')->setDecorators($this->elementDecorators)->addValidator('Digits'),
            $this->createElement('radio', 'menAblutions', array('separator' => '', 'multiOptions' => $yesNoArray))->setLabel('Salle ablutions hommes')->setDecorators($this->radioDecorators),
            $this->createElement('radio', 'womenAblutions', array('separator' => '', 'multiOptions' => $yesNoArray))->setLabel('Salle ablutions femmes')->setDecorators($this->radioDecorators),
            $this->createElement('radio', 'jumua', array('separator' => '', 'multiOptions' => $yesNoArray))->setLabel('Jumu\'a')->setDecorators($this->radioDecorators),
            $this->createElement('text', 'jumuaLanguage')->setLabel('Langue du prêche')->setDecorators($this->elementDecorators),
            $this->createElement('radio', 'islamLesson', array('separator' => '', 'multiOptions' => $yesNoArray))->setLabel('Cours islam')->setDecorators($this->radioDecorators),
            $this->createElement('radio', 'arabLesson', array('separator' => '', 'multiOptions' => $yesNoArray))->setLabel('Cours d\'arabe')->setDecorators($this->radioDecorators),
            //$this->createElement('radio', 'janaza', array('separator' => '', 'multiOptions' => $yesNoArray))->setLabel('Prière du mort')->setDecorators($this->radioDecorators),
            $this->createElement('radio', 'tarawih', array('separator' => '', 'multiOptions' => $yesNoArray))->setLabel('Tarawih')->setDecorators($this->radioDecorators),
            
            $captcha,
            $this->createElement('hash', get_class().'_csrf', array('salt' => 'unique', 'timeout' => 3600))->setDecorators($this->elementDecorators),
            $saveButton,
        ));
    }
    
    public function loadDefaultDecorators() {

        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'ul')),
            'Form'
        ));
    }

}

?>
