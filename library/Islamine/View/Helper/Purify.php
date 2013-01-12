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
   
   public function purifyTitle($title)
   {
        $title = strtolower($title);
        $search = array(' ', '!', '?', '.', ',', ';', '\'', '"', '(', ')', '[', ']', '&', '>', '<');
        $replace = array('-', '', '', '', '', '', '-', '-', '', '', '', '', '', '', '');
        $title = str_replace($search, $replace, $title);
        $title = strtolower(str_replace(array('à','á','â','ã','ä','å','ç','è','é','ê','ë','ì','í','î','ï','ð','ò','ó','ô,','õ','ö','ù','ú','û','ü','ý','ÿ'), array('a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','o','o','o','o,','o','o','u','u','u','u','y','y'), $title));
            
        if($title[strlen($title)-1] == '-')
            $title = substr ($title, 0, -1);
        return $title;
   }
}


?>
