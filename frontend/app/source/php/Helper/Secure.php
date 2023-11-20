<?php

namespace NavetSearch\Helper;
/**
 * Class Secure
 *
 * Provides methods for encrypting and decrypting data using AES-128-CTR encryption.
 * The encryption key and initialization vector are currently set as static variables,
 * but it is recommended to replace them with environment variables in a production environment.
 *
 * @package NavetSearch\Helper
 */
class Secure
{
    private static $ciphering = "AES-128-CTR";
    private static $vector = "2af8deebf4685c85"; //Change to env var, only test key
    private static $key = "QIUKEzIb6k"; //Change to env var, only test key

    /**
     * Encrypts the provided data using AES-128-CTR encryption.
     *
     * If the data is an array or object, it is first converted to a JSON string before encryption.
     *
     * @param mixed $data The data to be encrypted.
     * @return false|string|void The encrypted data, or false on failure.
     */
    public static function encrypt($data)
    {   
        if(is_array($data) || is_object($data)) {
            $data = json_encode($data);
        }
        return openssl_encrypt($data, self::$ciphering, self::$key, 0, self::$vector);
    }

     /**
     * Decrypts the provided encrypted data using AES-128-CTR decryption.
     *
     * If the decrypted data is a JSON string, it is converted back to an array or object.
     *
     * @param string $encryptedData The encrypted data to be decrypted.
     * @return mixed The decrypted data, or the original encrypted data on failure.
     */
    public static function decrypt($encryptedData)
    {   
        $decrypted =  openssl_decrypt($encryptedData, self::$ciphering, self::$key, 0, self::$vector);
        if(is_string($decrypted)) {
            $decrypted = json_decode($decrypted);
        }
        return $decrypted;
    }
}