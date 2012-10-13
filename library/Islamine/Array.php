<?php

class Islamine_Array
{
    public static function in_array_r($needle, $haystack) {
        foreach ($haystack as $item) {
            if ($item === $needle || (is_array($item) && in_array_r($needle, $item))) {
                return true;
            }
        }
        return false;
    }
    
    public static function array_to_object($array) {
        if(is_object($array))
            return $array;
        
        if(is_array($array))
        {
            $obj = new stdClass;
            foreach($array as $k => $v) {
                if(is_array($v)) {
                    $obj->{$k} = self::array_to_object($v); //RECURSION
                } else {
                    $obj->{$k} = $v;
                }
            }
            return $obj;
        }
        else
            return false;
    } 
}