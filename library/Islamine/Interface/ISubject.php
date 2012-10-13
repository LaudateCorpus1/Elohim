<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author jeremie
 */
interface Islamine_Interface_ISubject 
{
    public static function register($o);
    
    public function notify($flag, $row);
}

?>
