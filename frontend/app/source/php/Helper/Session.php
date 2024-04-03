<?php

namespace NavetSearch\Helper;

use NavetSearch\Interfaces\AbstractSecure;
use NavetSearch\Interfaces\AbstractSession;


class Session implements AbstractSession
{
    private static $authCookieName = "navet_auth_cookie";
    private static $authLength = 60 * 60 * 10;
    private AbstractSecure $secure;

    public function __construct(AbstractSecure $secure)
    {
        $this->secure = $secure;
    }

    /**
     * Sets the authentication cookie with encrypted user data.
     *
     * @param mixed $data The user data to be encrypted and stored in the authentication cookie.
     * @return bool Returns true if the cookie is successfully set, false otherwise.
     */
    public function set($data): bool
    {
        $couldSet = setcookie(
            self::$authCookieName,
            $this->secure->encrypt($data),
            [
                'expires' => time() + self::$authLength,
                'path' => '/',
                'domain' => $_SERVER['SERVER_NAME'] ?? '',
                'secure' => isset($_SERVER['HTTPS']) ? true : false,
                'httponly' => false,
                'samesite' => 'None'
            ]
        );

        if ($couldSet) {
            return true;
        }
        return false;
    }

    /**
     * Checks if the user is authenticated based on the presence of the authentication cookie.
     *
     * @return bool Returns true if the user is authenticated, false otherwise.
     */
    public function isValid(): bool
    {
        return (bool) $this->get();
    }

    /**
     * Retrieves user data from the authentication cookie.
     *
     * @return mixed|false The decrypted user data if the authentication cookie is present, false otherwise.
     */
    public function get(): mixed
    {
        if (isset($_COOKIE[self::$authCookieName])) {
            $data = $this->secure->decrypt(
                $_COOKIE[self::$authCookieName]
            );
            return $data;
        }
        return false;
    }

    /**
     * Logs out the user by deleting the authentication cookie.
     */
    public function end(): void
    {
        setcookie(
            self::$authCookieName,
            null,
            [
                'expires' => -1,
                'path' => '/',
                'domain' => $_SERVER['SERVER_NAME'] ?? '',
                'secure' => isset($_SERVER['HTTPS']) ? true : false,
                'httponly' => false,
                'samesite' => 'None'
            ]
        );
    }

    public function getAccountName(): string
    {
        if ($session = $this->get()) {
            return $session->samaccountname;
        }
        return 'unknown';
    }
}
