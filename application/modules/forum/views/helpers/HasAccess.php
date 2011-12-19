<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Zend_View_Helper_FavoriteTag
 *
 * @author jeremie
 */
class Zend_View_Helper_HasAccess extends Zend_View_Helper_Abstract
{
    private $_acl;
    
    public function hasAccess($resource, $privilege)
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $identity = $auth->getIdentity();
            $role = $identity->login.'_'.$identity->id;
            
            if (!$this->_acl)
                $acl = Zend_Registry::get('acl');
            
            return $acl->isAllowed($role, $resource, $privilege);

        }
        return false;
    }
}
?>
