<?php

class Islamine_Controller_Plugin_DescriptionModule extends Zend_Controller_Plugin_Abstract {

    /**
     * preDispatch()
     *
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $html = '<div id="description-module">
                    <p>Ce forum est destiné à développer la communauté musulmane. Les questions sont bien entendu
                    les bienvenues.</p>
                    <p>Vous etes aussi encouragés à poster des sujets qui traitent d\'un thème dans le but de faire 
                    participer les autres pour enrichir et fournir un texte de qualité sur ce thème.</p>
                </div>'; 
            /*Veillez cependant à poser des questions claire et compréhensible dans un langage
            correct pour que le forum soit agréable à lire (vous obtiendrez en plus des réponses de meilleure 
            qualité).*/
        $layout = Zend_Layout::getMvcInstance();
        $auth = Zend_Auth::getInstance();
        
        $layout->module_description = $html;

    }

}
