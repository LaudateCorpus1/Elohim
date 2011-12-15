<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Paginator
 *
 * @author jeremie
 */
class Islamine_Paginator extends Zend_Paginator
{
    public $pageName = 'page';
	
    public function getPages($scrollingStyle = null)
    {
        $pages = parent::getPages($scrollingStyle);
        $pages->pageName = $this->pageName;
        return $pages;
    }

    public function setPageName($pageName)
    {
        $this->pageName = $pageName;
    }
}

?>
