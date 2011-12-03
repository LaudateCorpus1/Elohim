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
}