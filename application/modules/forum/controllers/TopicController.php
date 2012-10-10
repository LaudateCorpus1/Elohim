<?php

class Forum_TopicController extends Zend_Controller_Action {

    public function init() {
        $this->_helper->layout->setLayout('forum_layout');
        
        if ($this->_request->isXmlHttpRequest()) {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();    //disable layout for ajax
        }
    }
    
    public function preDispatch()
    {
        if($this->_request->getActionName() == 'incrementvote' || $this->_request->getActionName() == 'decrementvote')
        {
            $maxVotesCast = intval(Zend_Registry::getInstance()->constants->max_votes_cast_per_day);
            $auth = Zend_Auth::getInstance();
            $model_karma = new Forum_Model_Karma();
            $userVotesCast = $model_karma->getTodayVotesCastByUser($auth->getIdentity()->id);
            if($userVotesCast >= $maxVotesCast)
            {
                $message = 'Vous avez atteint le quota maximum de vote par jour';
                $this->_forward('karma', 'error', 'forum', array('message' => $message));
            }
        }
    }

    public function indexAction() {
        $this->_forward('index', 'index');
    }

    public function showAction() {
        
        $id = $this->_getParam('topic');
        if ($id > 0) {
            
            $this->view->author = false;
            $i = 0;
            $topic = new Forum_Model_Topic();
            $messages = new Forum_Model_Message();
            $this->view->topic = $topic->getTopic($id)->toArray();
            
            /*
             * Formulaire de fermeture de topic
             * Un variable pour savoir si l'utilisateur est loggé est passé au script JS
             * pour éviter à envoyer la requete au serveur s'il n'est pas connecté
             */
            $form = new Forum_Form_CloseTopic();
            $auth = Zend_Auth::getInstance();
            $user_name = "";
            $autho = 'false';
            if($auth->hasIdentity())
            {
                $autho = 'true';
                $identity = $auth->getIdentity();
                $user_name = $identity->login;
                $this->view->identity = $identity->id;
                
                // L'auteur peut éditer son topic
                if($this->view->topic['userId'] == $identity->id) {
                    $this->view->author = true;
                }
                
                $form->populate(array('topic_id' => $id, 'username' => $user_name));
            }
            /*if ($this->view->topic['type'] == 'wiki') {
                $this->view->edit = true;
            }*/

            $r = $this->_getParam('messages');
            if($r != null) {
                $list = $r;
            }
            else
                $list = $topic->getMessagesFromTopic($id);
            
            $this->view->tags = $topic->getTagsFromTopic($id);
            
            if($this->view->topic['status'] != 'closed')
            {
                $close_motif = $topic->getMotifByTopic($id)->toArray();
                $this->view->nb_close_votes = count($close_motif);
                $this->view->close_motif = $close_motif;
            }
            else
            {
                $reopen_model = new Forum_Model_ReopenTopic();
                $this->view->nb_reopen_votes = $reopen_model->count($id);
            }

            $this->view->topicComments = $topic->getCommentsFromTopic($id);
   
            /*
             * Mise à jour du nombre de vues
             */
            $topic->incrementView($id);
            
            /*
             * Formulaire d'ajout de commentaire
             */
            $commentForm = new Forum_Form_UserPostComment();
            $this->view->commentForm = $commentForm;
            
            /*
             * Formulaire de réponse rapide
             */
            $messageForm = new Forum_Form_UserPostMessage();
            $this->view->messageForm = $messageForm;
            
            $page = new Islamine_Paginator(new Zend_Paginator_Adapter_DbSelect($list));
            $page->setPageRange(5);
            $page->setCurrentPageNumber($this->_getParam('page',1));
            $page->setItemCountPerPage(20);
            $this->view->messages = $page;
            
            foreach ($page as $message) {
                $this->view->$i = $messages->getCommentsFromMessage($message['messageId']);
                $i++;
            }
            
            $this->view->form = $form;
            $this->view->headScript()->appendScript("var auth = $autho;");
            $this->view->headScript()->appendFile("/js/answerEditor.js");
        }
    }

    public function answerAction() {
        
        $topicId = $this->_getParam('topic');
        $model_topic = new Forum_Model_Topic();
        $closed = $model_topic->isClosed($topicId);
        
        if(!$closed)
        {
            $messageForm = new Forum_Form_UserPostMessage();

            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();

                if ($messageForm->isValid($formData)) {
                    $auth = Zend_Auth::getInstance();
                    if($auth->hasIdentity())
                    {
                        $identity = $auth->getIdentity();
                        $content = $messageForm->getValue('form_message_content');

                        $message = new Forum_Model_Message();
                        $message->addMessage($identity->id, $topicId, $content, $_SERVER['REMOTE_ADDR']);

                        $model_topic->updateTopic(array('lastActivity' => gmdate('Y-m-d H:i:s', time())), $topicId);
                        $topic = $model_topic->getTopic($topicId);
                        if($topic->userId != $identity->id)
                            $this->_helper->notifyUser('Un nouveau message !', $topic->userId, $topicId);
                        
                        if($this->_request->isXmlHttpRequest())
                            echo Zend_Json::encode(array('status' => 'ok', 'user' => $identity->login, 'topicId' => $topicId, 'message' => $content));
                        else
                        {
                            $purifyHelper = $this->view->getHelper('Purify');
                            $this->_redirect('/forum/sujet/'.$topicId.'/'.$purifyHelper->purifyTitle($topic->title));
                        }
                    }
                }
            }
            $this->view->messageForm = $messageForm;
        }
        else
        {
            if($this->_request->isXmlHttpRequest())
                echo Zend_Json::encode(array('status' => 'error', 'message' => 'Ce sujet est fermé, vous ne pouvez pas y répondre'));
            else
                $this->view->message = 'Ce sujet est fermé, vous ne pouvez pas y répondre';
        }
    }

    public function addAction() {
        
        $this->view->headScript()->appendFile("/js/topicEditor.js");
        $topicForm = new Forum_Form_UserPostTopic();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($topicForm->isValid($formData)) {
                $tagArray = array();
                $title = $topicForm->getValue('form_topic_title');
                $message = $topicForm->getValue('form_topic_content');
                $tags = $topicForm->getValue('tagsValues');
                $tags = strtolower($tags);
                $tagArray = explode(" ", $tags);
                $topic = new Forum_Model_Topic();
                $tag = new Forum_Model_Tag();
                $topicTag = new Forum_Model_TopicTag();
                $auth = Zend_Auth::getInstance();
                
                $tag->getAdapter()->beginTransaction();
                
                $topicId = $topic->addTopic($auth->getIdentity()->id, $title, $message, $_SERVER['REMOTE_ADDR']);
                $error = false;
                foreach ($tagArray as $t) {
                    if ($tag->doesExist($t) !== false) {
                        $tagId = $tag->incrementTag($t);
                        $topicTag->addRow($topicId, $tagId);
                    } else {
                        $createTagsKarma = intval(Zend_Registry::getInstance()->constants->create_tags_karma);
                        if(intval($auth->getIdentity()->karma) < intval($createTagsKarma)) {
                            $tag->getAdapter()->rollBack();
                            $error = true;
                            $this->_forward('karma', 'error', 'forum', array('message' => 'Vous n\'avez pas le privilège pour créer des mots-clés'));
                        }
                        else {
                            $tagId = $tag->addTag($t, '1');
                            $topicTag->addRow($topicId, $tagId);
                        }
                    }
                }
                if(!$error) {
                    $tag->getAdapter()->commit();

                    $purifyHelper = $this->view->getHelper('Purify');
                    $title = $purifyHelper->purifyTitle($title);
                    $this->_redirect('/forum/sujet/'.$topicId.'/'.$title);
                }
            }
        }
        $this->view->topicForm = $topicForm;
    }

    public function tagAction() {
        $i = 0;
        $topics = new Forum_Model_Topic();
        $name = $this->view->tag = $this->_getParam('name');

        $list = $this->view->topics = $topics->getTopicsByTagName($name);
        
        $this->_forward('index', 'index', 'forum', array('topics' => $list));
    }

    public function incrementvoteAction() {
        
        $this->_helper->vote('UP_TOPIC');
    }

    public function decrementvoteAction() {
        
        $this->_helper->vote('DOWN_TOPIC');
    }

    public function editAction() {
        $topicId = $this->_getParam('id');
        $topic = new Forum_Model_Topic();
        $row = $topic->getTopic($topicId);
        
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $identity = $auth->getIdentity();
            // L'auteur peut éditer son topic
            if($row['userId'] == $identity->id)
            {
                $this->view->headScript()->appendFile("/js/topicEditor.js");
                $form = new Forum_Form_UserPostTopic();
                $namespace = new Zend_Session_Namespace('default');

                if ($this->getRequest()->isPost()) {
                    $formData = $this->getRequest()->getPost();

                    if ($form->isValid($formData)) {
                        $message = $form->getValue('form_topic_content');
                        $new_tags = $form->getValue('tagsValues');
                        
                        if($this->_helper->updateTags($topicId, $new_tags))
                        {
                            if (!$topic->editConfilct($namespace->mess, $topicId)) {
                                /*if ($row['type'] == 'wiki') {
                                    if ($row['lastEditTime'] == null) {
                                        $date = $row['date'];
                                    } else {
                                        $date = $row['lastEditTime'];
                                    }
                                    $wiki = new Forum_Model_WikiTopic();
                                    $wiki->addHistory($topicId, '1', $row['ipAddress'], $row['message'], $date);
                                }*/
                                $title = $form->getValue('form_topic_title');

                                $date = gmdate('Y-m-d H:i:s', time());
                                $topic->updateTopic(array('title' => $title, 'message' => $message, 'ipAddress' => $_SERVER['REMOTE_ADDR'], 'lastEditDate' => $date, 'lastActivity' => $date), $topicId);

                                $purifyHelper = $this->view->getHelper('Purify');
                                $title = $purifyHelper->purifyTitle($title);

                                $this->_redirect('forum/sujet/'.$topicId.'/'.$title);
                            } else {
                                $authorText = new Zend_Form_Element_Textarea('authorText');
                                $authorText->setLabel("Votre texte")
                                        ->setAttribs(array('rows' => '7', 'cols' => '50'))
                                        ->setValue($message);

                                $this->view->conflict = "Quelqu'un a modifié le texte pendant votre édition.
                                            Dans la zone de texte ci-dessus se trouve le texte tel qu'il est
                                            acutellement. Vos modifications se trouvent dans la zone de texte ci-dessous,
                                            veuillez les apporter dans la zone supérieure. Seule cette zone sera enregistrée.";

                                $this->view->authorText = $authorText;
                                $namespace->mess = $row['message'];
                            }
                        }
                        else
                            $this->_forward('karma', 'error', 'forum', array('message' => 'Vous n\'avez pas le privilège pour créer des mots-clés'));

                    }
                } else {
                    $namespace->mess = $row['message'];
                }

                $tags = $topic->getTagsFromTopic($topicId)->toArray();
                $tags_string = "";
                foreach ($tags as $tag)
                {
                    $tags_string .= $tag['name']. " ";
                }
                $tags_string = substr($tags_string, 0, -1);
                $form->populate(array('form_topic_title' => $row['title'], 'form_topic_content' => $row['message'], 'tagsValues' => $tags_string));
                $this->view->form = $form;
            }
            else
                throw new Exception('Vous n\'avez pas le droit de modifier le topic d\'un autre membre');
        }
    }

    /*protected function updateTags($topic_id, $new_tags)
    {
        $id = $topic_id;
        $topic = new Forum_Model_Topic();
        $tags = $topic->getTagsFromTopic($id)->toArray();
        $aOld_tag_name = array();
        foreach ($tags as $tag)
        {
            $aOld_tag_name[] = $tag['name'];
        }
        $tag_model = new Forum_Model_Tag();
        $aTags = array();
        $aTags = explode(" ", $new_tags);

        $aDiff_tags_old = array_diff($aOld_tag_name, $aTags);
        $aDiff_tags_new = array_diff($aTags, $aOld_tag_name);

        $topic_tag_model = new Forum_Model_TopicTag();

        foreach ($aDiff_tags_old as $tag) 
        {
            if (($tag_id = $tag_model->doesExist($tag)) !== false)
            {
                    $topic_tag_model->deleteRow ($id, $tag_id);
                    $tag_model->decrementTag($tag);
            }
        }

        foreach ($aDiff_tags_new as $tag) 
        {
            if (($tag_model->doesExist($tag)) !== false) 
            {
                $tag_id = $tag_model->incrementTag($tag);
            } 
            else 
            {
                $tag_id = $tag_model->addTag($tag, '1');
            }
            $topic_tag_model->addRow($id, $tag_id);
        }
    }*/
    
    public function alertAction()
    {
        $id = $this->_getParam('topic');
        $auth = Zend_Auth::getInstance();
        if ($id > 0 && $auth->hasIdentity()) 
        {
            $i = 0;
            $topic_model = new Forum_Model_Topic();
            $topic = $topic_model->getTopic($id);
            
            if($topic != null)
            {
                $this->view->topic = $topic->toArray();
                $form = new Forum_Form_Alert();
                $this->view->form = $form;
            }
            
            if ($this->getRequest()->isPost()) 
            {
                $formData = $this->getRequest()->getPost();
                if($form->isValid($formData)) 
                {
                    $motif = $form->getValue('motif');
                    $subject = 'Alerte sur le sujet '.$topic->title;
                    $body = 'Le sujet "'.$topic->title.'" posté par '.$topic->login.' contenant le message : 

'.strip_tags(($topic->message)).'
    
a été alerté par '.$auth->getIdentity()->login.' pour le motif : '.$motif;
                    
                    $this->_helper->alertMail($subject, $body);
                    
                    $this->view->message = 'Votre demande a été prise en compte.';
                }
            }
        }
    }
    
    public function closeAction()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $identity = $auth->getIdentity();
            $model_topic = new Forum_Model_Topic();
            if ($this->_request->isXmlHttpRequest()) 
            {
                $data = $this->getRequest()->getPost();
                $motif_model = new Forum_Model_CloseMotif();
                if($motif_model->hasAlreadyVoted($identity->id, $data['topic_id']))
                {
                    echo Zend_Json::encode(array('status' => 'error', 'message' => 'Vous avez déjà voté'));
                }
                else
                {
                    $motif_model->addMotif($data['topic_id'], $data['close_motif'], $identity->id);
                    $count = $motif_model->countMotif($data['topic_id']);
                    if($count == 7)
                    {
                        $model_topic->updateTopic(array('status' => 'closed'), $data['topic_id']);
                        $reopen_model = new Forum_Model_ReopenTopic();
                        $reopen_model->deleteByTopic($data['topic_id']);
                    }
                    echo Zend_Json::encode(array('status' => 'ok', 'count' => $count ));
                }
                
            }
            else
            {
                $form = new Forum_Form_CloseTopic();
                $form->addElement($form->createElement('submit', 'Valider'));
                $topic_id = $this->_getParam('topic');
                $form->populate(array('topic_id' => $topic_id, 'username' => $identity->login));
                $this->view->form = $form;

                $motif_model = new Forum_Model_CloseMotif();
                if($motif_model->hasAlreadyVoted($identity->id, $topic_id))
                        $this->view->message = 'Vous avez déjà voté';
                else
                {
                    if ($this->getRequest()->isPost()) 
                    {
                        $formData = $this->getRequest()->getPost();
                        if($form->isValid($formData)) 
                        {
                            $data = $this->getRequest()->getPost();

                            $topic_id = $form->getValue('topic_id');
                            $motif_model->addMotif($topic_id, $form->getValue('close_motif'), $identity->id);
                            $count = $motif_model->countMotif($data['topic_id']);
                            if($count == 7)
                            {
                                $model_topic->updateTopic(array('status' => 'closed'), $data['topic_id']);
                                $reopen_model = new Forum_Model_ReopenTopic();
                                $reopen_model->deleteByTopic($topic_id);
                            }
                            $this->_redirect('/forum/sujet/' . $topic_id);
                        }
                    }
                }
            }
        }
    }
    
    public function motifsAction()
    {
        $topic_model = new Forum_Model_Topic();
        $topic_id = $this->_getParam('topic');
        $close_motif = $topic_model->getMotifByTopic($topic_id)->toArray();
            
        if(count($close_motif) != 0)
            $this->view->close_motif = $close_motif;
    }
    
    public function reopenAction()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $identity = $auth->getIdentity();
            $model_topic = new Forum_Model_Topic();
            if ($this->_request->isXmlHttpRequest()) 
            {
                $data = $this->getRequest()->getPost();
                $reopen_model = new Forum_Model_ReopenTopic();
                if($reopen_model->hasAlreadyVoted($identity->id, $data['topic_id']))
                {
                    echo Zend_Json::encode(array('status' => 'error', 'message' => 'Vous avez déjà voté'));
                }
                else
                {
                    $reopen_model->addRow($data['topic_id'], $identity->id);
                    $count = $reopen_model->count($data['topic_id']);
                    if($count == 7)
                    {
                        $model_topic->updateTopic(array('status' => ''), $data['topic_id']);
                        $close_model = new Forum_Model_CloseMotif();
                        $close_model->deleteByTopic($data['topic_id']);
                    }
                    echo Zend_Json::encode(array('status' => 'ok', 'count' => $count ));
                }
            }
            else
            {
                $form = new Forum_Form_ReopenTopic();
                $topic_id = $this->_getParam('topic');
                $reopen_model = new Forum_Model_ReopenTopic();
                if($reopen_model->hasAlreadyVoted($identity->id, $topic_id))
                        $this->view->message = 'Vous avez déjà voté';
                else
                {
                    $this->view->form = $form;

                    if ($this->getRequest()->isPost()) 
                    {
                        $formData = $this->getRequest()->getPost();
                        if($form->isValid($formData)) 
                        {
                            $data = $this->getRequest()->getPost();
                            
                            $reopen_model->addRow($topic_id, $identity->id);
                            $count = $reopen_model->count($topic_id);
                            if($count == 7)
                            {
                                $model_topic->updateTopic(array('status' => ''), $topic_id);
                                $close_model = new Forum_Model_CloseMotif();
                                $close_model->deleteByTopic($topic_id);
                            }
                            $this->_redirect('/forum/sujet/'.$topic_id);
                        }
                    }
                }
            }
        }
    }

    public function sortAction()
    {
        $sort_type = $this->_getParam('t');
        $tag_name = $this->_getParam('name');
        
        if($sort_type == 'date')
        {
            if($tag_name != null)
                $this->_redirect ('/forum/topic/tag/name/'.$tag_name);
            else
                $this->_redirect ('/forum');
        }
        else
        {
            $model_topic = new Forum_Model_Topic();
            //$closed_flag = $this->_helper->hasAccess('forum_topic', 'close');
            $topics_sorted = $model_topic->sortTopics($sort_type, true, $tag_name);
            $this->_forward('index', 'index', 'forum', array('topics' => $topics_sorted));
        }
    }
    
    public function commentAction() {
        
        $topicId = $this->_getParam('topic');
        $model_topic = new Forum_Model_Topic();
        $closed = $model_topic->isClosed($topicId);
        
        if(!$closed)
        {
            $commentForm = new Forum_Form_UserPostComment();

            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();

                if ($commentForm->isValid($formData)) {
                    $this->_processCommentForm($formData, $topicId);
                }
                else
                {
                    if($this->_request->isXmlHttpRequest())
                        echo Zend_Json::encode(array('status' => 'error', 'message' => 'Le commentaire n\'est pas valide'));
                }
            }
            $this->view->commentForm = $commentForm;
        }
        else
        {
            if($this->_request->isXmlHttpRequest())
                echo Zend_Json::encode(array('status' => 'error', 'message' => 'Ce sujet est fermé, vous ne pouvez pas y écrire de commentaire'));
            else
                $this->view->message = 'Ce sujet est fermé, vous ne pouvez pas y écire de commentaire';
        }
    }

    protected function _processCommentForm($data, $topicId)
    {
        $content = $data['form_comment_content'];
        $comment = new Forum_Model_Comment();
        $model_topic = new Forum_Model_Topic();
        $commentTopic = new Forum_Model_CommentTopic();
        
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $identity = $auth->getIdentity();
            $commentDate = gmdate('Y-m-d H:i:s', time());
            $commentId = $comment->addComment($identity->id, $content, $commentDate);
            $commentTopic->addRow($commentId, $topicId);
            $model_topic->updateTopic(array('lastActivity' => gmdate('Y-m-d H:i:s', time())), $topicId);
            
            if ($this->_request->isXmlHttpRequest()) {
                echo Zend_Json::encode(array('status' => 'ok', 'user' => $identity->login, 'userId' => $identity->id, 'commentId' => $commentId, 'date' => $commentDate));
            } else {
                $this->_redirect('/forum/sujet/' . $topicId);
            }
        }
    }
}

