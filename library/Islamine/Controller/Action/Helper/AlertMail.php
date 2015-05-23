<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Islamine_Controller_Action_Helper_AlertMail extends Zend_Controller_Action_Helper_Abstract
{
    public function direct($from, $subject, $body)
    {
        $sender = '';
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
            $sender = $auth->getIdentity()->login;
            
        $mail = new Islamine_Mail('moderation%islamine.com', '!SSAARRLL22!');
        $mail->addTo('moderation@islamine.com', 'Modération Islamine');    
        $mail->setFrom($from, 'Alerte par '.$sender);
        $mail->setSubject($subject); //'15L4M1N3';
        $mail->setBodyText($body, 'utf-8');
        return $mail->send();
    }
    
    public function send($subject, $body, $sendto)
    {
        $auth = Zend_Auth::getInstance();
        $mail = new Islamine_Mail('moderation%islamine.com', '!SSAARRLL22!');
        $mail->addTo($sendto, $sendto);    
        $mail->setFrom('moderation@islamine.com', 'Islamine');
        $mail->setSubject($subject);
        $mail->setBodyText($body, 'utf-8');
        return $mail->send();
    }
}


?>
