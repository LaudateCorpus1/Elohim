<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Tags
 *
 * @author jeremie
 */
class Islamine_Validate_Tags extends Zend_Validate_Abstract
{
    const MSG_MAXIMUM = 'msgMaximum';
    
    public $maximum = 5;
    
    protected $_messageVariables = array(
        'max' => 'maximum'
    );
 
    protected $_messageTemplates = array(
        self::MSG_MAXIMUM => "Vous ne pouvez pas mettre plus de %max% mots clés."
    );
 
    public function isValid($value)
    {
        $this->_setValue($value);
        
        $count = mb_substr_count($value, ' ');
 
        if ($count > ($this->maximum - 1)) {
            $this->_error(self::MSG_MAXIMUM);
            return false;
        }
 
        return true;
    }
}

?>
