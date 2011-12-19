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
class Zend_View_Helper_TextWrap extends Zend_View_Helper_Abstract
{
    public function textWrap($string, $width)
    {

        /*if (mb_strlen($string, 'utf-8') > $width)
        {
            $dom = new DOMDocument();
            $dom->loadHTML($string);
            foreach ($dom->getElementsByTagName('*') as $elem)
            {
                foreach ($elem->childNodes as $node)
                {
                   /* while($node->nodeType == XML_ELEMENT_NODE)
                    {
                        $node = $node->childNodes;
                    }*/
                   /* if ($node->nodeType === XML_TEXT_NODE)
                    {
                        $text = trim($node->nodeValue);
                        $length = mb_strlen($text);
                        $width -= $length;
                         var_dump($length, $text, $width); 
                        if($width <= 0)
                        { 
                            $string = wordwrap($node->nodeValue, 1);
                            //while ($elem->hasChildNodes()) 
                            {   
                            
                                //$elem->removeChild($elem->firstChild);
                            }
                            
                            //$string = $dom->saveHTML();
                            $string = ' [...]';
                            var_dump($string);
                            return $string;
                        }
                    }

                }
            }
        }
        //exit;
        /*$results = $dom->query("*");
        foreach($results as $element)
        {
            var_dump($element->valid());
        }
        exit;*/
        /*$res = '';
        if (mb_strlen($string, 'utf-8') > $width)
        {
            $string = Islamine_String::wordWrapIgnoreHTML($string, $width, '<br />'); 
            $string = mb_substr($string, 0, mb_strpos($string, "<br />", 0, 'utf-8'), 'utf-8');
            $string .= ' [...]';
        }*/
        
    $stripped = trim(strip_tags($string));
    if(mb_strlen($stripped, 'utf-8') > $width)
    {
            $stripped = wordwrap($stripped, $width, '<br />');
            $stripped = mb_substr($stripped, 0, mb_strpos($stripped, "<br />", 0, 'utf-8'), 'utf-8');
            $stripped .= ' [...]';
    }
    return $stripped;

        
        //return $string;
    }
}
?>
