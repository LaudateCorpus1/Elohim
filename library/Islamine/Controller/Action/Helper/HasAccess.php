<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Islamine_Controller_Action_Helper_HasAccess extends Zend_Controller_Action_Helper_Abstract
{
    private $_acl;
    
    public function direct($resource, $privilege)
    {
        if (!$this->_acl)
            $acl = Zend_Registry::get('acl');
        
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $identity = $auth->getIdentity();
            $role = $identity->login.'_'.$identity->id;
            if(!$acl->hasRole($role))
                $role = $identity->role;
        }
        else
        {
            $role = 'guest';
        }
        return $acl->isAllowed($role, $resource, $privilege);
    }
}


?>
