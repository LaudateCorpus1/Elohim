<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Acl_Ini
 *
 * @author jeremie
 */
class Islamine_Acl extends Zend_Acl
{
    private $_karma_privileges;
    private $_config;


    public function __construct($file, $config)	
    {
        $this->_config = $config;
        
        $roles = new Zend_Config_Ini($file, 'roles') ;
        $this->_setRoles($roles) ;

        $ressources = new Zend_Config_Ini($file, 'ressources') ;
        $this->_setRessources($ressources) ;

        foreach ($roles->toArray() as $role => $parents)	
        {
            $privileges = new Zend_Config_Ini($file, $role) ;
            $this->_setPrivileges($role, $privileges) ;
        }
        
        $this->_setKarmaPrivileges();
        $this->_setUserPrivileges();
    }
	
    protected function _setRoles($roles)	
    {
        foreach ($roles as $role => $parents)	
        {
            if (empty($parents))
                $parents = null ;
            else 
                $parents = explode(',', $parents) ;

            $this->addRole(new Zend_Acl_Role($role), $parents);
        }

        return $this ;
    }

    protected function _setRessources($ressources)	
    {
        foreach ($ressources as $ressource => $parents)	
        {
            if (empty($parents))	
                $parents = null ;
            else 
                $parents = explode(',', $parents) ;

            $this->add(new Zend_Acl_Resource($ressource), $parents);
        }

        return $this ;
    }

    protected function _setPrivileges($role, $privileges)	
    {
        foreach ($privileges as $do => $ressources)	
        {
            foreach ($ressources as $ressource => $actions)	
            {
                if (empty($actions))	
                    $actions = null ;
                else
                    $actions = explode(',', $actions) ;
                
                $this->{$do}($role, $ressource, $actions);
            }
        }

        return $this ;
    }
    
    protected function _setKarmaPrivileges()
    {
        $model_privileges = new Model_Privileges($this->_config);
        $this->_karma_privileges = $model_privileges->getAll();
    }
    
    /*public function _getKarmaPrivileges()
    {
        return $this->_karma_privileges;
    }*/
    
    /*
     * Définit le role spécifique de chaque utilisateur (i.e. les actions de karma)
     */
    protected function _setUserPrivileges()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity())
        {
            $identity = $auth->getIdentity();
            
            /*
             * On vérifie d'abord si l'utilisateur est à jour
             */
            $model_user = new Model_User($this->_config);
            $user = $model_user->get($identity->id, true);
            //Zend_Debug::dump($user); exit;
            Zend_Registry::set('user', $user);
            
            $role = $identity->login.'_'.$identity->id;
            if(!$this->hasRole($role)) 
                $this->addRole(new Zend_Acl_Role($role), $identity->role);

            foreach($this->_karma_privileges as $privilege)
            {
                if(intval($user->karma) >= $privilege->karma_needed)
                {
                    $this->allow($role, $privilege->module.'_'.$privilege->resource, $privilege->privilege);
                }
            }
        }
    }

}

?>
