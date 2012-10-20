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
class Islamine_View_Helper_Submenu extends Zend_View_Helper_Abstract
{
    public function submenu()
    {
        $modelCategory = new Model_Category();
        $categories = $modelCategory->getNames();
        $html = '<ul class="subnav">';
        foreach($categories as $category)
        {
            $html .= '<li><a href="#">'.$category.'</a></li>';
        }
        $html .= '</ul>';
        return $html;
    }
}


?>
