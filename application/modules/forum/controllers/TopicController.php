<?php

class Forum_TopicController extends Zend_Controller_Action {

    public function init() {
        $this->_helper->layout->setLayout('forum_layout');
        
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
            $i = 0;
            $this->view->edit = false;
            $topic = new Forum_Model_Topic();
            $messages = new Forum_Model_Message();
            $this->view->topic = $topic->getTopic($id);

            /*if ($this->view->topic['type'] == 'wiki') {
                $this->view->edit = true;
            }*/

            $list = $this->view->messages = $topic->getMessagesFromTopic($id);
            $this->view->tags = $topic->getTagsFromTopic($id);

            foreach ($list as $message) {
                $this->view->$i = $messages->getCommentsFromMessage($message->messageId);
                $i++;
            }
        }
    }

    public function answerAction() {
        $messageForm = new Forum_Form_UserPostMessage();
        $this->view->messageForm = $messageForm;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if ($messageForm->isValid($formData)) {
                $content = $messageForm->getValue('content');
                $topicId = $this->_getParam('topic');
                $message = new Forum_Model_Message();
                $message->addMessage('1', $topicId, $content, $_SERVER['REMOTE_ADDR']);
                $this->_redirect('/topic/show/topic/' . $topicId);
            }
        }
    }

    public function addAction() {
        $topicForm = new Forum_Form_UserPostTopic();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($topicForm->isValid($formData)) {
                $tagArray = array();
                $title = $topicForm->getValue('title');
                $message = $topicForm->getValue('content');
                $tags = $topicForm->getValue('tagsValues');
                $tagArray = explode(" ", $tags);
                $topic = new Forum_Model_Topic();
                $tag = new Forum_Model_Tag();
                $topicTag = new Forum_Model_TopicTag();

                $topicId = $topic->addTopic('1', $title, $message, $_SERVER['REMOTE_ADDR']);

                foreach ($tagArray as $t) {
                    if ($tag->doesExist($t)) {
                        $tagId = $tag->incrementTag($t);
                        $topicTag->addRow($topicId, $tagId);
                    } else {
                        $tagId = $tag->addTag($t, '1');
                        $topicTag->addRow($topicId, $tagId);
                    }
                }

                $this->_redirect('/topic/show/topic/' . $topicId);
            }
        }
        $this->view->topicForm = $topicForm;
    }

    public function tagAction() {
        $i = 0;
        $topics = new Forum_Model_Topic();
        $name = $this->_getParam('name');

        $list = $this->view->topics = $topics->getTopicsByTagName($name);
        foreach ($list as $topic) {
            $this->view->$i = $topics->getTagsFromTopic($topic->topic_topicId);
            $i++;
        }
    }

    public function incrementvoteAction() {
        $incrementTopic = new Forum_Model_Topic();
        $this->view->topic = $topicId = $this->_getParam('topic');
        $vote = $incrementTopic->incrementVote($topicId);
        if ($this->_request->isXmlHttpRequest())
                echo $vote;
    }

    public function decrementvoteAction() {
        $decrementTopic = new Forum_Model_Topic();
        $this->view->topic = $topicId = $this->_getParam('topic');
        $vote = $decrementTopic->decrementVote($topicId);
        if ($this->_request->isXmlHttpRequest())
                echo $vote;
    }

    public function editAction() {
        $topicId = $this->_getParam('topic');
        $topic = new Forum_Model_Topic();
        $row = $topic->getTopic($topicId);
        $form = new Forum_Form_UserPostMessage();
        $namespace = new Zend_Session_Namespace('default');


        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if ($form->isValid($formData)) {
                $message = $form->getValue('content');

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

                    $topic->updateTopic(array('message' => $message, 'ipAddress' => $_SERVER['REMOTE_ADDR'], 'lastEditTime' => date('Y-m-d H:i:s', time())), $topicId);
                    $this->_redirect('/topic/show/topic/' . $topicId);
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

        $form->setDefaultMessage($row['message']);
        $this->view->form = $form;
    }

}

