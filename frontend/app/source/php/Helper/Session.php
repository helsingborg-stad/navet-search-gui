<?php

declare(strict_types=1);

namespace NavetSearch\Helper;

use NavetSearch\Interfaces\AbstractSecure;
use NavetSearch\Interfaces\AbstractConfig;
use NavetSearch\Interfaces\AbstractSession;
use NavetSearch\Interfaces\AbstractCookie;

class Session implements AbstractSession
{
    private string $name;
    private string $expires;
    private AbstractSecure $secure;
    private AbstractCookie $cookie;

    public function __construct(AbstractConfig $config, AbstractSecure $secure, AbstractCookie $cookie)
    {
        // Read config
        $this->name = $config->getValue(
            'SESSION_COOKIE_NAME',
            "navet_auth_cookie"
        );
        $this->expires = (string) $config->getValue(
            'SESSION_COOKIE_EXPIRES',
            (string) 60 * 60 * 10
        );
        // Encryption/Decryption
        $this->secure = $secure;
        // Cookie management
        $this->cookie = $cookie;
    }

    /**
     * Sets the authentication cookie with encrypted user data.
     *
     * @param mixed $data The user data to be encrypted and stored in the authentication cookie.
     * @return bool Returns true if the cookie is successfully set, false otherwise.
     */
    public function set(mixed $data): bool
    {
        $options = [
            'expires' => (time() + (int) $this->expires)
        ];
        return $this->cookie->set(
            $this->name,
            $this->secure->encrypt($data),
            $options
        );
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
        $value = $this->cookie->get($this->name);

        if (isset($value)) {
            return $this->secure->decrypt($value);
        }
        return false;
    }

    /**
     * Logs out the user by deleting the authentication cookie.
     */
    public function end(): void
    {
        $this->cookie->set($this->name);
    }

    /**
     * Returns the account name as stored in the cookie data
     * 
     * @return string The accountname of the userdata
     */
    public function getAccountName(): string|false
    {
        if ($session = $this->get()) {
            return $session->samaccountname;
        }
        return false;
    }
}
