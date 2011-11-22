<?php
require_once APPLICATION_PATH . '/../library/htmlpurifier/library/HTMLPurifier.includes.php';
require_once APPLICATION_PATH . '/../library/htmlpurifier/library/HTMLPurifier.autoload.php';

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Islamine_Filter_HtmlPurifier
 *
 * @author jeremie
 */
class Islamine_Filter_HtmlPurifier implements Zend_Filter_Interface
{
    protected $_htmlPurifier = null;

    public function __construct($options = null)
    {
        // set up configuration
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.DefinitionID', 'My Filter');
        $config->set('HTML.DefinitionRev', 1); // increment when configuration changes
        // $config->set('Cache.DefinitionImpl', null); // comment out after finalizing the config

        // Doctype
        $config->set('HTML.Doctype', 'HTML 4.01 Transitional');

        // configure caching
        /*$cachePath = APPLICATION_PATH . '/../cache/htmlpurifier';
        if (!is_dir($cachePath)) {
            mkdir($cachePath, 0755, true);
        }
        $cachePath = realpath($cachePath);
        $config->set('Cache.SerializerPath', $cachePath);*/

        if (!is_null($options)) {
            //$config = HTMLPurifier_Config::createDefault();
            foreach ($options as $option) {
                $config->set($option[0], $option[1], $option[2]);
            }
        }

        $this->_htmlPurifier = new HTMLPurifier($config);
    }

    public function filter($value)
    {
        return $this->_htmlPurifier->purify($value);
    }
}


?>
