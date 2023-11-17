<?php

namespace HbgStyleGuide\Helper;

class Secure
{
    private static $ciphering = "AES-128-CTR";
    private static $vector = "2af8deebf4685c85"; //Change to env var, only test key
    private static $key = "QIUKEzIb6k"; //Change to env var, only test key

    public static function encrypt($data)
    {   
        if(is_array($data) || is_object($data)) {
            $data = json_encode($data);
        }
        return openssl_encrypt($data, self::$ciphering, self::$key, 0, self::$vector);
    }

    public static function decrypt($encryptedData)
    {   
        $decrypted =  openssl_decrypt($encryptedData, self::$ciphering, self::$key, 0, self::$vector);
        if(is_string($decrypted)) {
            $decrypted = json_decode($decrypted);
        }
        return $decrypted;
    }
}