<?php

namespace NavetSearch\Helper;
use \NavetSearch\Helper\Secure as Secure;
use \NavetSearch\Helper\Format as Format;

/**
 * Class User
 *
 * Provides methods for handling user authentication, including setting authentication cookies,
 * checking authentication status, retrieving user data from cookies, and logging out.
 *
 * @package NavetSearch\Helper
 */
class User
{
    private static $authCookieName = "navet_auth_cookie"; 
    private static $authLength = 60 * 60 * 10;

    /**
     * Sets the authentication cookie with encrypted user data.
     *
     * @param mixed $data The user data to be encrypted and stored in the authentication cookie.
     * @return bool Returns true if the cookie is successfully set, false otherwise.
     */
    public static function set($data) {
        $couldSet = setcookie(
            self::$authCookieName, 
            Secure::encrypt($data), 
            [
                'expires' => time() + self::$authLength,
                'path' => '/',
                'domain' => $_SERVER['SERVER_NAME'] ?? '',
                'secure' => true,
                'httponly' => false,
                'samesite' => 'None'
            ]
        ); 

        if($couldSet) {
            return true;
        }
        return false;
    }

    /**
     * Checks if the user is authenticated based on the presence of the authentication cookie.
     *
     * @return bool Returns true if the user is authenticated, false otherwise.
     */
    public static function isAuthenticated() {
        return (bool) self::get(); 
    }

    /**
     * Retrieves user data from the authentication cookie.
     *
     * @return mixed|false The decrypted user data if the authentication cookie is present, false otherwise.
     */
    public static function get() {
        if(isset($_COOKIE[self::$authCookieName])) {
            $data = Secure::decrypt(
                $_COOKIE[self::$authCookieName]
            );
            return $data;            
        }
        return false; 
    }

    /**
     * Retrieve and format the user data.
     *
     * @return array|false Formatted user data or false if user data is not available.
     */
    public static function getFormattedUser() {
        if($user = self::get()) {
            return Format::user($user);
        }
        return false;
    }

    /**
     * Logs out the user by deleting the authentication cookie.
     */
    public static function logout() {
        setcookie(
            self::$authCookieName, 
            null, 
            [
                'expires' => -1,
                'path' => '/',
                'domain' => $_SERVER['SERVER_NAME'] ?? '',
                'secure' => true,
                'httponly' => false,
                'samesite' => 'None'
            ]
        );
    }
}