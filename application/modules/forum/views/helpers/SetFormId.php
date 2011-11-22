<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Zend_View_Helper_FavoriteTag
 *
 * @author jeremie
 */
class Zend_View_Helper_SetFormId extends Zend_View_Helper_Abstract
{
    public function setFormId($form, $id)
    {
        $form->setAttrib('id', $id);
    }
}
?>
