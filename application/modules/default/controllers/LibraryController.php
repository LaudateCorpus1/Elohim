<?php

class LibraryController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout->setLayout('layout');
        
        if ($this->_request->isXmlHttpRequest()) {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();    //disable layout for ajax
        }
    }

    public function indexAction()
    {
        $library = $this->_getParam('documents');
        $autho = 'false';
        $author = false;
        $this->view->username = $username = $this->_getParam('username');
        
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            if(strtolower($auth->getIdentity()->login) == strtolower($username))
                $author = true;
            $autho = 'true'; // Utilisateur authentifié
        }
        $this->view->author = $author;
        
        $modelLibrary = new Default_Model_Library();
        $this->view->title = 'Bibliothèque de '.$username;
        if($this->_getParam('name'))
            $this->view->title = 'Documents sur '.$this->_getParam('name');
        if($library == null) {
            $library = $modelLibrary->getByUsername($username, $author);
        }
        
        $page = new Islamine_Paginator(new Zend_Paginator_Adapter_DbSelect($library));
        $page->setPageRange(5);
        $page->setCurrentPageNumber($this->_getParam('page',1));
        $page->setItemCountPerPage(20);
        $this->view->library = $page;
        $i = 0;
        foreach ($page as $document)
        {
            $this->view->$i = $modelLibrary->getTags($document['id']);
            $i++;
        }
        $this->view->headScript()->appendScript("var auth = $autho;");
    }
    
    public function addAction()
    {
        $this->view->headScript()->appendFile("/js/documentEditor.js");
        $form = new Default_Form_CompleteLibrary();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $tagArray = array();
                $title = $form->getValue('form_document_library_header');
                $description = $form->getValue('form_document_library_description');
                $public = $form->getValue('form_document_library_public');
                $source = $form->getValue('form_document_library_source');
                $tags = $form->getValue('tagsValues');
                $tags = strtolower($tags);
                $tagArray = explode(" ", $tags);
                $modelLibrary = new Default_Model_Library();
                $tag = new Forum_Model_Tag();
                $modelLibraryTag = new Default_Model_LibraryTag();
                $auth = Zend_Auth::getInstance();
                
                $tag->getAdapter()->beginTransaction();
                
                $libraryId = $modelLibrary->addDocument($auth->getIdentity()->id, $title, $description, $public, $source);
                $error = false;
                foreach ($tagArray as $t) {
                    if ($tag->doesExist($t) !== false) {
                        $tagId = $tag->incrementTag($t, 'libraryAmount');
                        $modelLibraryTag->addRow($libraryId, $tagId);
                    } else {
                        $createTagsKarma = intval(Zend_Registry::getInstance()->constants->create_tags_karma);
                        if(intval($auth->getIdentity()->karma) < intval($createTagsKarma)) {
                            $tag->getAdapter()->rollBack();
                            $error = true;
                            $this->_forward('karma', 'error', 'forum', array('message' => 'Vous n\'avez pas le privilège pour créer des mots-clés'));
                        }
                        else {
                            $tagId = $tag->addTag($t, '1', 'libraryAmount');
                            $modelLibraryTag->addRow($libraryId, $tagId);
                        }
                    }
                }
                if(!$error) {
                    $tag->getAdapter()->commit();

                    $purifyHelper = $this->view->getHelper('Purify');
                    $title = $purifyHelper->purifyTitle($title);
                    $this->_redirect('/library/'.$auth->getIdentity()->login.'/doc/'.$libraryId.'/'.$title);
                }
            }
        }
        $this->view->form = $form;
    }
    
    public function showAction()
    {
        $auth = Zend_Auth::getInstance();
        $id = $this->_getParam('document');
        $modelLibrary = new Default_Model_Library();
        $this->view->document = $modelLibrary->get($id);
        
        $author = false;
        $autho = 'false';
        if($auth->hasIdentity())
        {
            $autho = 'true';
            if($auth->getIdentity()->id == $this->view->document->userId)
                $author = true;
        }
        $this->view->author = $author;
        if(!$author && intval($this->view->document->public) == 0)
            throw new Exception ('Ce document n\'est pas public');
        
        $this->view->tags = $modelLibrary->getTags($id);
        $this->view->comments = $modelLibrary->getComments($id);
        
        // Formulaire d'alerte
        $this->view->form = new Default_Form_DocumentAlert();
        // Formulaire de commentaire
        $commentForm = new Default_Form_CommentDocument();
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if (isset($formData['post_comment']) && $commentForm->isValid($formData)) {
                $this->_processComment($formData, $id);
            }
        }
        $this->view->commentForm = $commentForm;
        
        $this->view->headScript()->appendScript("var auth = $autho;");
    }
    
    public function editAction()
    {
       $auth = Zend_Auth::getInstance();
       $id = $this->_getParam('id');
       $modelLibrary = new Default_Model_Library();
       $document = $modelLibrary->get($id);
       
       // Si c'est bien l'auteur du doc
       if($auth->getIdentity()->id == $document->userId)
       {
           $this->view->headScript()->appendFile("/js/documentEditor.js");
           $form = new Default_Form_CompleteLibrary();

            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                if ($form->isValid($formData)) {
                    
                    $title = $form->getValue('form_document_library_header');
                    $description = $form->getValue('form_document_library_description');
                    $public = $form->getValue('form_document_library_public');
                    $source = $form->getValue('form_document_library_source');
                    $newTags = $form->getValue('tagsValues');
                    if($this->_helper->updateTags($id, $newTags, 'library'))
                    {
                        $date = date('Y-m-d H:i:s', time());
                        $modelLibrary->updateDocument(array(
                                                        'title' => $title,
                                                        'content' => $description,
                                                        'lastEditDate' => $date,
                                                        'public' => $public,
                                                        'source' => $source
                                                     ), $id);

                        $purifyHelper = $this->view->getHelper('Purify');
                        $title = $purifyHelper->purifyTitle($title);
                        $this->_redirect('/doc/show/'.$id.'/'.$title);
                    }
                    else
                        throw new Exception ('Vous n\'avez pas le privilège pour créer des mots-clés');
                }
            }
            $tags = $modelLibrary->getTags($id)->toArray();
            $tags_string = "";
            foreach ($tags as $tag)
            {
                $tags_string .= $tag['name']. " ";
            }
            $tags_string = substr($tags_string, 0, -1);
            $form->populate(array(
                            'form_document_library_header' => $document->title,
                            'form_document_library_description' => $document->content,
                            'form_document_library_public' => $document->public,
                            'form_document_library_source' => $document->source,
                            'tagsValues' => $tags_string
                           ));
            $this->view->form = $form;
       }
       else
           throw new Exception('Vous n\'avez pas le droit de modifier un document ne vous appartenant pas');
    }
    
    public function deleteAction()
    {
        $id = $this->_getParam('id');
        if($this->isAuthor($id))
        {
            $modelLibrary = new Default_Model_Library();
            $modelLibrary->deleteDocument($id);
            $auth = Zend_Auth::getInstance();
            
            if($this->_request->isXmlHttpRequest())
                echo Zend_Json::encode(array('status' => 'ok', 'username' => $auth->getIdentity()->login));
            else
                $this->view->message = 'Le document a été supprimé';
        }
        else
        {
            $message = 'Vous n\'avez pas le droit de supprimer un document ne vous appartenant pas';
            if($this->_request->isXmlHttpRequest())
                echo Zend_Json::encode(array('status' => 'error', 'message' => $message));
            else
                throw new Exception($message);
        }
    }
    
    private function isAuthor($docId)
    {
       $auth = Zend_Auth::getInstance();
       $modelLibrary = new Default_Model_Library();
       $userId = $modelLibrary->getUserId($docId);
       if($userId == $auth->getIdentity()->id)
           return true;
       else
           return false;
    }
    
    public function alertAction()
    {
        $id = $this->_getParam('id');
        if ($id > 0) 
        {
            $libraryModel = new Default_Model_Library();
            $document = $libraryModel->get($id);
            
            if ($this->_request->isXmlHttpRequest()) 
            {
                $data = $this->getRequest()->getPost();
                
                $motif = $data['motif'];
                $this->flagDocument($motif, $document);
                echo Zend_Json::encode(array('status' => 'ok', 'message' => 'Votre demande a été prise en compte.'));
            }
            else
            {
                $this->view->document = $document;
                $this->view->form = $form = new Default_Form_DocumentAlert();

                if ($this->getRequest()->isPost()) 
                {
                    $formData = $this->getRequest()->getPost();
                    if($form->isValid($formData)) 
                    {
                        $motif = $form->getValue('motif');
                        $this->flagDocument($motif, $document);
                        $this->view->message = 'Votre demande a été prise en compte.';
                    }
                }
            }
        }
    }
    
    private function flagDocument($motif, $document)
    {
        $auth = Zend_Auth::getInstance();
        $libraryModel = new Default_Model_Library();
        $libraryModel->updateDocument(array('flag' => true), $document->id);
        
        $subject = 'Alerte sur le document '.$document->id. ' : '.$document->title;
                        $body = 'Le document "'.$document->title.'" posté par '.$document->login.' contenant le message : 
'.strip_tags(($document->content)).'
a été alerté par '.$auth->getIdentity()->login.' pour le motif : '.$motif;

        $this->_helper->alertMail($subject, $body);
    }
    
    public function incrementvoteAction() {
        $documentId = $this->getRequest()->getParam('document');
        $modelLibrary = new Default_Model_Library();
        if(!$modelLibrary->isPublic($documentId))
        {
            if ($this->getRequest()->isXmlHttpRequest())
                echo Zend_Json::encode(array('status' => 'error', 'message' => 'Ce document n\'est pas public'));
            else
                throw new Exception ('Ce document n\'est pas public');
        }
        else
            $this->_helper->vote('UP_DOCUMENT');
    }
    
    public function decrementvoteAction() {
        $documentId = $this->getRequest()->getParam('document');
        $modelLibrary = new Default_Model_Library();
        if(!$modelLibrary->isPublic($documentId))
        {
            if ($this->getRequest()->isXmlHttpRequest())
                echo Zend_Json::encode(array('status' => 'error', 'message' => 'Ce document n\'est pas public'));
            else
                throw new Exception ('Ce document n\'est pas public');
        }
        else
            $this->_helper->vote('DOWN_DOCUMENT');
    }
    
    public function addfavoriteAction()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $identity = $auth->getIdentity();
            $modelLibrary = new Default_Model_Library();
            $documentId = $this->_getParam('id');
            $modelFavoriteLibrary = new Default_Model_FavoriteLibrary();
            
            if($modelLibrary->isPublic($documentId))
            {
                if($modelLibrary->alreadyFavorited($documentId, $identity->id))
                {
                    if($this->_request->isXmlHttpRequest())
                        echo Zend_Json::encode(array('status' => 'error', 'message' => 'Ce document est déjà en favoris'));
                    else
                        $this->view->message = 'Ce document est déjà en favoris';
                }
                else
                {
                    $modelFavoriteLibrary->addRow($identity->id, $documentId);
                    if($this->_request->isXmlHttpRequest())
                        echo Zend_Json::encode(array('status' => 'ok', 'action' => 'add', 'documentId' => $documentId));
                    else
                        $this->view->message = 'Le document a été mis en favoris';
                }
            }
            else
                throw new Exception ('Ce document n\'est pas public', 500);
        }
    }
    
    public function removefavoriteAction()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $identity = $auth->getIdentity();
            $modelLibrary = new Default_Model_Library();
            $documentId = $this->_getParam('id');
            $modelFavoriteLibrary = new Default_Model_FavoriteLibrary();
            
            if($modelLibrary->alreadyFavorited($documentId, $identity->id))
            {
                $modelFavoriteLibrary->deleteRow($identity->id, $documentId);
                if($this->_request->isXmlHttpRequest())
                    echo Zend_Json::encode(array('status' => 'ok', 'action' => 'remove', 'documentId' => $documentId));
                else
                    $this->view->message = 'Le document a été retiré en favoris';
            }
            else
            {
                if($this->_request->isXmlHttpRequest())
                    echo Zend_Json::encode(array('status' => 'error', 'message' => 'Ce document n\'est pas en favoris'));
                else
                    $this->view->message = 'Ce document n\'est pas en favoris';
            }
        }
    }
    
    protected function _processComment($data, $documentId)
    {
        $content = $data['form_comment_content'];
        $modelComment = new Default_Model_CommentLibrary();
        
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $identity = $auth->getIdentity();
            $data = array(
                        'libraryId' => $documentId,
                        'userId' => $identity->id,
                        'date' => date('Y-m-d H:i:s', time()),
                        'content' => $content
            );
            $commentId = $modelComment->addComment($data);
            
            if ($this->_request->isXmlHttpRequest()) {
                echo Zend_Json::encode(array('status' => 'ok', 'user' => $identity->login, 'userId' => $identity->id, 'commentId' => $commentId, 'date' => $commentDate));
            } else {
                $this->_redirect($this->view->url());
            }
        }
    }
    
    public function tagAction() {
        $modelLibrary = new Default_Model_Library();
        $name = $this->view->tag = $this->_getParam('name');

        $list = $this->view->documents = $modelLibrary->getDocumentsByTagName($name);
        
        $this->_forward('index', 'library', 'default', array('documents' => $list));
    }
}

