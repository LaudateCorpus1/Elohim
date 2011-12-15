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
class Islamine_View_Helper_Purify extends Zend_View_Helper_Abstract
{
   public function purify($value)
   {
       $options = array(
           array(
               //'HTML.AllowedElements', 'span,a,p,ol,ul,li', null
               'HTML.Allowed', 'span[class],a[href|title],p,ol,ul,li,img[src|title|alt]', null
           )
       );
       $filter = new Islamine_Filter_HtmlPurifier($options);

       return $filter->filter($value);
   }
}


?>
