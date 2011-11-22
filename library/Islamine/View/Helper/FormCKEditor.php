<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Islamine_View_Helper_FormCKEditor
 *
 * @author jeremie
 */
class Islamine_View_Helper_FormCKEditor extends Zend_View_Helper_FormTextarea
{
    static $instances = 0;
 
    public function formCKEditor($name, $value = null, $attribs = null)
    {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable
 
        $xhtml = $this->formTextarea($name, $value, $attribs);
        $xhtml .= '<script type="text/javascript"><!--mce:0--></script>';
        self::$instances += 1;
        if (self::$instances == 1) {
            $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/ckeditor/ckeditor.js');
        }
        return $xhtml;
    }
}

?>
