<?php

declare(strict_types=1);

namespace NavetSearch\Helper;

use NavetSearch\Interfaces\AbstractCookie;

/**
 * Wrapper class to allow cookie management during tests
 */
class Cookie implements AbstractCookie
{
    public function set(string $key, mixed $data = "", mixed $options = null): bool
    {
        // Default values are set to remove a cookie, these values could
        // be overriden with the options parameter.
        $merge = array_merge([
            'expires' => -1,
            'path' => '/',
            'domain' => $_SERVER['SERVER_NAME'],
            'secure' => isset($_SERVER['HTTPS']) ? true : false,
            'httponly' => false,
            'samesite' => 'None'
        ], $options ?? array());

        // Set native cookie
        return setcookie($key, $data, $merge);
    }
    public function get(string $key): string|null
    {
        // Return native cookie
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
    }
}
