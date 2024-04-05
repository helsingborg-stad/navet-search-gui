<?php

declare(strict_types=1);

namespace NavetSearch\Helper;

use NavetSearch\Interfaces\AbstractSecure;
use NavetSearch\Interfaces\AbstractConfig;
use NavetSearch\Interfaces\AbstractSession;
use NavetSearch\Interfaces\AbstractCookie;
use NavetSearch\Interfaces\AbstractUser;

class Session implements AbstractSession
{
    protected string $name;
    protected string $expires;
    protected AbstractSecure $secure;
    protected AbstractCookie $cookie;

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

    public function getSessionName(): string
    {
        return $this->name;
    }
    public function getSessionExpiration(): int
    {
        return (int) $this->expires;
    }

    /**
     * Sets the authentication cookie with encrypted user data.
     *
     * @param mixed $data The user data to be encrypted and stored in the authentication cookie.
     * @return bool Returns true if the cookie is successfully set, false otherwise.
     */
    public function setSession(AbstractUser $user): bool
    {
        $options = [
            'expires' => (time() + (int) $this->expires)
        ];
        return $this->cookie->set(
            $this->name,
            $this->secure->encrypt($user),
            $options
        );
    }

    /**
     * Checks if the user is authenticated based on the presence of the authentication cookie.
     *
     * @return bool Returns true if the user is authenticated, false otherwise.
     */
    public function isValidSession(): bool
    {
        return (bool) $this->getUser();
    }

    /**
     * Retrieves user data from the authentication cookie.
     *
     * @return mixed|false The decrypted user data if the authentication cookie is present, false otherwise.
     */
    public function getUser(): AbstractUser|false
    {
        $value = $this->cookie->get($this->name);

        if (isset($value)) {
            return new User((object) $this->secure->decrypt($value));
        }
        return false;
    }

    /**
     * Logs out the user by deleting the authentication cookie.
     */
    public function endSession(): void
    {
        $this->cookie->set($this->name);
    }
}
