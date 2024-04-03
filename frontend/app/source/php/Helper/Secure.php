<?php

namespace NavetSearch\Helper;

use NavetSearch\Interfaces\AbstractSecure;
use NavetSearch\Interfaces\AbstractConfig;

/**
 * Class Secure
 *
 * Provides methods for encrypting and decrypting data using AES-128-CTR encryption.
 * The encryption key and initialization vector are currently set as static variables,
 * but it is recommended to replace them with environment variables in a production environment.
 *
 * @package NavetSearch\Helper
 */
class Secure implements AbstractSecure
{
    private string $ciphering = "AES-128-CTR";
    private string $vector;
    private string $key;

    public function __construct(AbstractConfig $config)
    {
        $this->vector = $config->get('ENCRYPT_VECTOR') ?? "";
        $this->key = $config->get('ENCRYPT_KEY') ?? "";
    }

    /**
     * Encrypts the provided data using AES-128-CTR encryption.
     *
     * If the data is an array or object, it is first converted to a JSON string before encryption.
     *
     * @param mixed $data The data to be encrypted.
     * @return false|string|void The encrypted data, or false on failure.
     */
    public function encrypt($data): string|false
    {
        if (is_array($data) || is_object($data)) {
            $data = json_encode($data);
        }
        return openssl_encrypt($data, $this->ciphering, $this->key, 0, $this->vector);
    }

    /**
     * Decrypts the provided encrypted data using AES-128-CTR decryption.
     *
     * If the decrypted data is a JSON string, it is converted back to an array or object.
     *
     * @param string $encryptedData The encrypted data to be decrypted.
     * @return mixed The decrypted data, or the original encrypted data on failure.
     */
    public function decrypt($encryptedData): mixed
    {
        $decrypted = openssl_decrypt($encryptedData, $this->ciphering, $this->key, 0, $this->vector);
        if (is_string($decrypted)) {
            $decrypted = json_decode($decrypted);
        }
        return $decrypted;
    }
}
