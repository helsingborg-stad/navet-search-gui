<?php

declare(strict_types=1);

namespace NavetSearch\Helper;

use Closure;
use NavetSearch\Interfaces\AbstractCookie;

/**
 * Wrapper class to allow cookie management during tests
 */
class Cookie implements AbstractCookie
{
    protected Closure $setcookie;
    protected array $server;
    protected array $cookie;

    public function __construct()
    {
        $this->server = $_SERVER;
        $this->cookie = $_COOKIE;
        $this->setcookie = function (string $key, mixed $data, mixed $options) {
            return setcookie($key, $data, $options);
        };
    }
    public function set(string $key, mixed $data = "", mixed $options = null): bool
    {
        // Default values are set to remove a cookie, these values could
        // be overriden with the options parameter.
        $merge = array_merge([
            'expires' => -1,
            'path' => '/',
            'domain' => $this->server['SERVER_NAME'],
            'secure' => isset($this->server['HTTPS']) ? true : false,
            'httponly' => false,
            'samesite' => 'None'
        ], $options ?? array());

        // Set native cookie
        return ($this->setcookie)($key, $data, $merge);
    }
    public function get(string $key): string|null
    {
        // Return native cookie
        return isset($this->cookie[$key]) ? $this->cookie[$key] : null;
    }
}