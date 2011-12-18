<?php

class Forum_TopicController extends Zend_Controller_Action {

    public function init() {
        $this->_helper->layout->setLayout('forum_layout');
        
        $view = Zend_Layout::getMvcInstance()->getView();
        $view->addHelperPath(APPLICATION_PATH . '/../library/Islamine/View/Helper', 'Islamine_View_Helper_');

        
        if ($this->_request->isXmlHttpRequest()) {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();    //disable layout for ajax
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
                if($this->view->topic['userId'] == $identity->id)
                {
                    $this->view->author = true;
                }
                
                $form->populate(array('topic_id' => $id, 'username' => $user_name));
            }
            /*if ($this->view->topic['type'] == 'wiki') {
                $this->view->edit = true;
            }*/

            $list = $this->view->messages = $topic->getMessagesFromTopic($id);
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

            foreach ($list as $message) {
                $this->view->$i = $messages->getCommentsFromMessage($message->messageId);
                $i++;
            }
            
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

                        $topic_author = $model_topic->getAuthor($topicId);
                        if($topic_author->userId != $identity->id)
                            $this->_helper->notifyUser('Un nouveau message !', $topic_author->userId, $topicId);
                        
                        if($this->_request->isXmlHttpRequest())
                            echo Zend_Json::encode(array('status' => 'ok', 'user' => $identity->login, 'topicId' => $topicId, 'message' => $content));
                        else
                            $this->_redirect('/forum/topic/show/topic/' . $topicId);
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
                $tagArray = explode(" ", $tags);
                $topic = new Forum_Model_Topic();
                $tag = new Forum_Model_Tag();
                $topicTag = new Forum_Model_TopicTag();
                $auth = Zend_Auth::getInstance();
                
                $topicId = $topic->addTopic($auth->getIdentity()->id, $title, $message, $_SERVER['REMOTE_ADDR']);

                foreach ($tagArray as $t) {
                    if ($tag->doesExist($t) !== false) {
                        $tagId = $tag->incrementTag($t);
                        $topicTag->addRow($topicId, $tagId);
                    } else {
                        $tagId = $tag->addTag($t, '1');
                        $topicTag->addRow($topicId, $tagId);
                    }
                }

                $this->_redirect('/forum/topic/show/topic/' . $topicId);
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
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();
        $incrementTopic = new Forum_Model_Topic();
        $this->view->topic = $topicId = $this->_getParam('topic');
        $model_vote = new Forum_Model_Vote();
        
        if($model_vote->alreadyVoted($identity->id, $topicId, 'UP_TOPIC'))
        {
            if($this->_request->isXmlHttpRequest())
                echo Zend_Json::encode(array('status' => 'error', 'message' => 'Vous avez déjà voté'));
            else
                $this->view->message = 'Vous avez déjà voté';
        }
        else
        {
            $res = $incrementTopic->incrementVote($topicId, $identity->id);
            if($res === false)
            {
                if ($this->_request->isXmlHttpRequest())
                    echo Zend_Json::encode(array('status' => 'error', 'message' => 'Vous ne pouvez pas voter pour vous'));
                else
                    $this->view->message = 'Vous ne pouvez pas voter pour vous';
            }
            else
            {
                $user_model = new Model_User();

                $revote = false;

                if($model_vote->alreadyVoted($identity->id, $topicId, 'DOWN_TOPIC'))
                {
                    $revote = true;
                    // Il faut annuler l'ancien vote
                    $model_vote->deleteVote($identity->id, $topicId, 'TOPIC');
                    $karma_up = Zend_Registry::getInstance()->constants->vote_topic_down_reward;
                    $user_model->setKarma(abs(intval($karma_up)), $res->userId);
                }
                else 
                {
                    $karma_up = Zend_Registry::getInstance()->constants->vote_topic_up_reward;
                    $user_model->setKarma($karma_up, $res->userId);
                    $model_vote->addVote($identity->id, $topicId, 'UP_TOPIC');
                }

                if ($this->_request->isXmlHttpRequest())
                    echo Zend_Json::encode(array('status' => 'ok', 'vote' => $res->vote, 'type' => 'UP_TOPIC', 'revote' => $revote));
                else
                    $this->view->message = 'Merci d\'avoir voté';
            }
        }
    }

    public function decrementvoteAction() {
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();
        $incrementTopic = new Forum_Model_Topic();
        $this->view->topic = $topicId = $this->_getParam('topic');
        $model_vote = new Forum_Model_Vote();
        
        if($model_vote->alreadyVoted($identity->id, $topicId, 'DOWN_TOPIC'))
        {
            if($this->_request->isXmlHttpRequest())
                echo Zend_Json::encode(array('status' => 'error', 'message' => 'Vous avez déjà voté'));
            else
                $this->view->message = 'Vous avez déjà voté';
        }
        else
        {
            $res = $incrementTopic->decrementVote($topicId, $identity->id);
            if($res === false)
            {
                if ($this->_request->isXmlHttpRequest())
                    echo Zend_Json::encode(array('status' => 'error', 'message' => 'Vous ne pouvez pas voter pour vous'));
                else
                    $this->view->message = 'Vous ne pouvez pas voter pour vous';
            }
            else // OK
            {
                $user_model = new Model_User();

                $revote = false;

                if($model_vote->alreadyVoted($identity->id, $topicId, 'UP_TOPIC'))
                {
                    $revote = true;
                    // Il faut annuler l'ancien vote
                    $model_vote->deleteVote($identity->id, $topicId, 'TOPIC');
                    $karma_up = Zend_Registry::getInstance()->constants->vote_topic_up_reward;
                    $user_model->setKarma('-'.$karma_up, $res->userId);
                }
                else 
                {
                    $karma_up = Zend_Registry::getInstance()->constants->vote_topic_down_reward;
                    $user_model->setKarma($karma_up, $res->userId);
                    $model_vote->addVote($identity->id, $topicId, 'DOWN_TOPIC');
                }

                if ($this->_request->isXmlHttpRequest())
                    echo Zend_Json::encode(array('status' => 'ok', 'vote' => $res->vote, 'type' => 'DOWN_TOPIC', 'revote' => $revote));
                else
                    $this->view->message = 'Merci d\'avoir voté';
            }
        }
    }

    public function editAction() {
        $topicId = $this->_getParam('topic');
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
                            $new_tags = $form->getValue('tagsValues');

                            $topic->updateTopic(array('title' => $title, 'message' => $message, 'ipAddress' => $_SERVER['REMOTE_ADDR'], 'lastEditTime' => date('Y-m-d H:i:s', time())), $topicId);
                            $this->updateTags($topicId, $new_tags);
                            
                            $this->_redirect('forum/topic/show/topic/' . $topicId);
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
                $this->_redirect ('/default/error/error');
        }
    }

    protected function updateTags($topic_id, $new_tags)
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
    }
    
    public function alertAction()
    {
        $id = $this->_getParam('topic');
        if ($id > 0) 
        {
            $i = 0;
            $topic_model = new Forum_Model_Topic();
            $topic = $topic_model->find($id)->current();
            
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
                   
                    $mail = new Islamine_Mail('jeremie.paas@gmail.com', '!SSAARRLL22!');
                    $mail->addTo('jeremie.paas@gmail.com', 'Test');    
                    $mail->setFrom('jeremie.paas@gmail.com', 'Test');
                    $mail->setSubject(' Envoyé  emails par connexion SMTP');
                    $mail->setBodyText(' le Message .éé.............. Motif : '.$motif, 'utf-8');
                    $mail->send();
                    
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
                $motif_model->addMotif($data['topic_id'], $data['close_motif'], $identity->id);
                $count = $motif_model->countMotif($data['topic_id']);
                if($count == 7)
                {
                    $model_topic->updateTopic(array('status' => 'closed'), $data['topic_id']);
                    $reopen_model = new Forum_Model_ReopenTopic();
                    $reopen_model->deleteByTopic($data['topic_id']);
                }
                echo $count;
            }
            else
            {
                $form = new Forum_Form_CloseTopic();
                $form->addElement($form->createElement('submit', 'Valider'));
                $topic_id = $this->_getParam('topic');
                $form->populate(array('topic_id' => $topic_id, 'username' => $identity->login));
                $this->view->form = $form;

                if ($this->getRequest()->isPost()) 
                {
                    $formData = $this->getRequest()->getPost();
                    if($form->isValid($formData)) 
                    {
                        $data = $this->getRequest()->getPost();
                        $motif_model = new Forum_Model_CloseMotif();

                        $topic_id = $form->getValue('topic_id');
                        $motif_model->addMotif($topic_id, $form->getValue('close_motif'), $identity->id);
                        $count = $motif_model->countMotif($data['topic_id']);
                        if($count == 7)
                        {
                            $model_topic->updateTopic(array('status' => 'closed'), $data['topic_id']);
                            $reopen_model = new Forum_Model_ReopenTopic();
                            $reopen_model->deleteByTopic($topic_id);
                        }
                        $this->_redirect('/forum/topic/show/topic/' . $topic_id);
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
                $reopen_model->addRow($data['topic_id'], $identity->id);
                $count = $reopen_model->count($data['topic_id']);
                if($count == 7)
                {
                    $model_topic->updateTopic(array('status' => ''), $data['topic_id']);
                    $close_model = new Forum_Model_CloseMotif();
                    $close_model->deleteByTopic($data['topic_id']);
                }
                echo $count;
            }
            else
            {
                $form = new Forum_Form_ReopenTopic();
                $topic_id = $this->_getParam('topic');
                $this->view->form = $form;

                if ($this->getRequest()->isPost()) 
                {
                    $formData = $this->getRequest()->getPost();
                    if($form->isValid($formData)) 
                    {
                        $data = $this->getRequest()->getPost();
                        $reopen_model = new Forum_Model_ReopenTopic();
                        $reopen_model->addRow($topic_id, $identity->id);
                        $count = $reopen_model->count($topic_id);
                        if($count == 7)
                        {
                            $model_topic->updateTopic(array('status' => ''), $topic_id);
                            $close_model = new Forum_Model_CloseMotif();
                            $close_model->deleteByTopic($topic_id);
                        }
                        $this->_redirect('/forum/topic/show/topic/' . $topic_id);
                    }
                }
            }
        }
    }

    public function sortAction()
    {
        $sort_type = $this->_getParam('t');
            
        if($sort_type == 'date')
            $this->_redirect ('/forum');
        else
        {
            $model_topic = new Forum_Model_Topic();
            $topics_sorted = $model_topic->sortTopics($sort_type);
            $this->_forward('index', 'index', 'forum', array('topics' => $topics_sorted));
        }
    }
}

