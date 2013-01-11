<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SearchIndexer
 *
 * @author jeremie
 */
class Islamine_SearchIndexer implements Islamine_Interface_IObserver
{
    protected $_indexDirectory;
    
    public function __construct($indexDirectory)
    {
        $this->_indexDirectory = $indexDirectory;
        try
        {
            $index = Zend_Search_Lucene::open($this->_indexDirectory);
        } 
        catch (Exception $e)
        {
            $index = Zend_Search_Lucene::create($this->_indexDirectory);
        }
    }
    
    public function setIndexDirectory($directory)
    {
        $this->_indexDirectory = $directory;
    }
    
    public function getIndexDirectory()
    {
        return $this->_indexDirectory;
    }
        
    //this is the function invoked by the subject (Observer pattern)
    public function update($flag, $row)
    {
        $doc = new Zend_Search_Lucene_Document();
        if ($row['class'] == 'Library')
        {
            $fields = $row;
            
            // docRef sert à retrouver le document pour le supprimer ou mettre à jour
            // Lucene ne supporte pas la maj, il faut supprimer puis ajouter
            $doc->addField(Zend_Search_Lucene_Field::Keyword('docRef', $fields['class'].':'.$fields['id']));
            $doc->addField(Zend_Search_Lucene_Field::UnIndexed('class', $fields['class']));
            $doc->addField(Zend_Search_Lucene_Field::UnIndexed('key', $fields['id']));
            $doc->addField(Zend_Search_Lucene_Field::text('title', $fields['title'], 'utf-8'));
            $doc->addField(Zend_Search_Lucene_Field::UnIndexed('vote', $fields['vote']));
            $doc->addField(Zend_Search_Lucene_Field::text('content', $fields['content'], 'utf-8'));
            $doc->addField(Zend_Search_Lucene_Field::UnIndexed('date', $fields['date']));
            $doc->addField(Zend_Search_Lucene_Field::UnIndexed('userId', $fields['userId']));
            $doc->addField(Zend_Search_Lucene_Field::UnIndexed('categoryId', $fields['categoryId']));
            $doc->addField(Zend_Search_Lucene_Field::UnIndexed('login', $fields['login'], 'utf-8'));
        }
        $this->_modifyIndex($flag, $doc);
    }
    
    protected function _modifyIndex($flag, Zend_Search_Lucene_Document $doc)
    {
        $docRef = $doc->docRef;
        $index = Zend_Search_Lucene::open($this->_indexDirectory);
        if ($flag != 'insert')
        {
            $term = new Zend_Search_Lucene_Index_Term($docRef, 'docRef');
            $query = new Zend_Search_Lucene_Search_Query_Term($term);
            $hits = $index->find($query);
            if (count($hits) > 0)
            {
                foreach ($hits as $hit)
                {
                    $index->delete($hit->id);
                }
            }
        }
        
        if ($flag != 'delete')
        {
            $index->addDocument($doc);
            $index->optimize();
        }
    }
}

?>
