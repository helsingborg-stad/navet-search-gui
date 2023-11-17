<?php

namespace HbgStyleGuide\Helper;

use \HbgStyleGuide\Helper\Sanitize as Sanitize;

class Validate
{
    public static function pnr($string)
    {   
        if(strlen(Sanitize::number($string)) == 12) {
            return true; 
        }
        return false;
    }

    public static function empty($string)
    {
        return (bool) !empty($string); 
    }

    public static function email($string)
    {  
        if (filter_var($string, FILTER_VALIDATE_EMAIL)) {
            return true; 
        }
        return false; 
    }

    public static function username(string $string)
    {   
        if(preg_match('/^[A-Za-z]{4}[0-9]{4}$/', $string)){
            return true; 
        }
        return false;
    }

    public static function password(string $string)
    {   
        return self::empty($string);
    }
}
