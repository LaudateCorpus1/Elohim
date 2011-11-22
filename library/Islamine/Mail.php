<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Mail
 *
 * @author jeremie
 */
class Islamine_Mail
{
    private $_config;
    
    private $_mail;
    
    private $_transport;
    
    public function __construct($username, $password)
    {
        $this->_config = array(
                        'ssl' => 'tls',
                        'port' => 587,
                        'auth' => 'login',
                        'username' => $username,
                        'password' => $password
                    );
        
        $this->_transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $this->_config);
        Zend_Mail::setDefaultTransport($this->_transport);
        $this->_mail = new Zend_Mail('utf-8'); 
    }
    
    public function addTo($mail, $name)
    {
        $this->_mail->addTo($mail, $name);  
    }
    
    public function setFrom($mail, $name)
    {
        $this->_mail->setFrom($mail, $name);  
    }
    
    public function setSubject($subject)
    {
        $this->_mail->setSubject($subject); 
    }
    
    public function setBodyText($text)
    {
        $this->_mail->setBodyText($text);
    }
    
    public function send()
    {
        $this->_mail->send($this->_transport);
    }
}

?>
