<?php

namespace HbgStyleGuide\Helper;
use \HbgStyleGuide\Helper\Secure as Secure;

class User
{
    private static $authCookieName = "navet_auth_cookie"; 
    private static $authLength = 60 * 60 * 10;

    public static function set($data) {
        $couldSet = setcookie(
            self::$authCookieName, 
            Secure::encrypt($data), 
            time() + self::$authLength, 
            "/"
        ); 

        if($couldSet) {
            return true;
        }
        return false;
    }

    public static function isAuthenticated() {
        return (bool) self::get(); 
    }

    public static function get() {
        if(isset($_COOKIE[self::$authCookieName])) {
            $data = Secure::decrypt(
                $_COOKIE[self::$authCookieName]
            );
            return $data;            
        }
        return false; 
    }

    public static function logout() {
        setCookie(self::$authCookieName, null, -1, "/"); 
    }
}