<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Autocomplete
 *
 * @author jeremie
 */
class Zend_View_Helper_Autocomplete extends Zend_View_Helper_FormElement{
    public function autocomplete()
    {
        return '<ul id="tags"></ul>';
    }
}
?>
