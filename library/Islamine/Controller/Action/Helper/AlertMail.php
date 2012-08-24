<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Islamine_Controller_Action_Helper_AlertMail extends Zend_Controller_Action_Helper_Abstract
{
    public function direct($subject, $body)
    {
        $auth = Zend_Auth::getInstance();
        $mail = new Islamine_Mail('jeremie.paas@gmail.com', '!SSAARRLL22!');
        $mail->addTo('moderation.islamine@gmail.com', 'Modération Islamine');    
        $mail->setFrom('jeremie.paas@gmail.com', 'Alerte par '.$auth->getIdentity()->login);
        $mail->setSubject($subject); //'15L4M1N3';
        $mail->setBodyText($body, 'utf-8');
        $mail->send();
    }
    
    public function send($subject, $body, $sendto)
    {
        $auth = Zend_Auth::getInstance();
        $mail = new Islamine_Mail('moderation.islamine@gmail.com', '15L4M1N3');
        $mail->addTo($sendto, $sendto);    
        $mail->setFrom('moderation.islamine@gmail.com', 'Islamine');
        $mail->setSubject($subject);
        $mail->setBodyText($body, 'utf-8');
        $mail->send();
    }
}


?>
