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
class Zend_View_Helper_DocumentTitle extends Zend_View_Helper_Abstract
{
    public function documentTitle($document)
    {
        if(is_array($document))
            $document = Islamine_Array::array_to_object ($document);
        
        $purifyHelper = $this->view->getHelper('Purify');
        $title = $this->view->escape($purifyHelper->purifyTitle($document->title));
        $comp = '';
        
        if(filter_var($this->view->escape($document->title), FILTER_VALIDATE_URL))
        {
            $comp = ' - <a href="'.$this->view->escape($document->title).'" target="_blank">Aller sur le site</a>';
            if(strpos($title, '//') !== false)
            {
                $title = substr($title, strpos($title, '//')+2);
                if($title[strlen($title)-1] == '/')
                    $title = substr ($title, 0, -1);
                $title = str_replace('/', '-', $title);
            }
        }
        
        if(isset($document->key) && $document->key != null)
            $id = $this->view->escape($document->key);
        else
            $id = $this->view->escape($document->id);
        
        $html = '<a href="'.$this->view->url(array(
                                            'document' => $id,
                                            'title' => $title
                                        ), 'showDocument').'">'.$this->view->escape($document->title).'
                 </a>'.$comp;
        
        return $html;
    }
}
?>
