<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Acl_Ini
 *
 * @author jeremie
 */
class Islamine_Sitemap
{
    private $_doc;
    
    private $_staticURL;
    
    private $_path;
    
    public function __construct()	
    {
       $this->_doc = new DOMDocument('1.0', 'UTF-8');
       $this->_doc->formatOutput = true;
       
       $this->_staticURL = array();
       $this->_staticURL[] = array(
           'loc' => 'http://www.islamine.com',
           'priority' => '0.5',
           'changefreq' => 'monthly'
       );
       
       $this->_staticURL[] = array(
           'loc' => 'http://www.islamine.com/news',
           'priority' => '0.6',
           'changefreq' => 'weekly'
       );
       
       $this->_staticURL[] = array(
           'loc' => 'http://www.islamine.com/doc/list',
           'priority' => '0.8',
           'changefreq' => 'daily'
       );
       
       $this->_staticURL[] = array(
           'loc' => 'http://www.islamine.com/faq',
           'priority' => '0.4',
           'changefreq' => 'monthly'
       );
    }
	
    public function buildSitemap($path)	
    {
        $this->_path = $path;
        
        $root = $this->_doc->createElement('urlset');
        $this->_doc->appendChild($root);
        $this->_doc->createAttributeNS("http://www.sitemaps.org/schemas/sitemap/0.9", 'xmlns');
        
        $attribute = $this->_doc->createAttribute('xmlns:xsi');
        $attribute->value = "http://www.w3.org/2001/XMLSchema-instance";
        $root->appendChild($attribute);
        $attribute = $this->_doc->createAttribute('xsi:schemaLocation');
        $attribute->value = "http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd";
        $root->appendChild($attribute);
        
        foreach($this->_staticURL as $staticURL)
        {
            $url = $this->_doc->createElement('url');

            $loc = $this->_doc->createElement('loc');
            $loc->appendChild($this->_doc->createTextNode($staticURL['loc']));
            $url->appendChild($loc);

            $priority = $this->_doc->createElement('priority');
            $priority->appendChild($this->_doc->createTextNode($staticURL['priority']));
            $url->appendChild($priority);

            $freq = $this->_doc->createElement('changefreq');
            $freq->appendChild($this->_doc->createTextNode($staticURL['changefreq']));
            $url->appendChild($freq);

            $root->appendChild($url);
        }
        
        $this->addLastDocuments($root);
        
        $res = $this->_doc->save($this->_path);
    }
    
    protected function addLastDocuments($root)
    {
        
        $modelLibrary = new Default_Model_Library();
        $documents = $modelLibrary->getWithLimit();
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');
        $titleHelper = $view->getHelper('DocumentTitle');
        
        foreach($documents as $document)
        {
            $location = 'http://www.islamine.com'.$titleHelper->documentTitleRaw($document);
            $url = $this->_doc->createElement('url');

            $loc = $this->_doc->createElement('loc');
            $loc->appendChild($this->_doc->createTextNode($location));
            $url->appendChild($loc);

            $priority = $this->_doc->createElement('priority');
            $priority->appendChild($this->_doc->createTextNode('0.7'));
            $url->appendChild($priority);

            $freq = $this->_doc->createElement('changefreq');
            $freq->appendChild($this->_doc->createTextNode('weekly'));
            $url->appendChild($freq);

            $root->appendChild($url);
        }
    }
    
    public function getJSONSitemap()
    {
        $response = $this->_staticURL;
        
        $modelLibrary = new Default_Model_Library();
        $documents = $modelLibrary->getWithLimit();
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');
        $titleHelper = $view->getHelper('DocumentTitle');
        
        foreach($documents as $document)
        {
            $location = 'http://www.islamine.com'.$titleHelper->documentTitleRaw($document);
            $response[] = array(
               'loc' => $location,
               'priority' => '0.7',
               'changefreq' => 'weekly'
            );
        }
        return Zend_JSON::encode($response);
    }
}

?>
