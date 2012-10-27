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
class Zend_View_Helper_DocumentCategory extends Zend_View_Helper_Abstract
{
    public function documentCategory($document)
    {
        if(is_array($document))
            $document = Islamine_Array::array_to_object ($document);
        switch ($document->categoryId)
        {
            case '1': $icon = 'icon-align-justify';
                break;
            
            case '2': $icon = 'icon-music';
                break;
            
            case '3': $icon = 'icon-film';
                break;
            
            case '4': $icon = 'icon-picture';
                break;
            
            default: $icon = 'icon-asterisk';
                break;
        }
        $html = '<span title="Document '.lcfirst($document->category).'">
                    <i class="'.$icon.'"></i>
                </span>';
        
        return $html;
    }
}
?>
