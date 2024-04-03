<?php

namespace NavetSearch\Helper;

use NavetSearch\Enums\AuthErrorReason;
use NavetSearch\Interfaces\AbstractSession;
use NavetSearch\Interfaces\AbstractRequest;
use NavetSearch\Interfaces\AbstractAuth;
use NavetSearch\Interfaces\AbstractConfig;
use NavetSearch\Helper\AuthException;

class Auth implements AbstractAuth
{
    private AbstractRequest $request;
    private ?AbstractSession $session;
    private string $MS_AUTH;
    private string|array $AD_GROUPS;

    public function __construct(AbstractConfig $config, AbstractRequest $request, AbstractSession $session = null)
    {
        $this->request = $request;
        $this->session = $session;
        $this->MS_AUTH = rtrim($config->get('MS_AUTH') ?? "", "/");
        $this->AD_GROUPS = $config->get('AD_GROUPS') ?? "";
    }

    public function authenticate(string $name, string $password): object
    {
        $response = $this->request->post($this->MS_AUTH . '/user/current', [
            'username' => $name,
            'password' => Sanitize::password($password)
        ]);
        //Check http response
        if ($response->isErrorResponse()) {
            throw new AuthException(AuthErrorReason::HttpError);
        }
        // Check response data
        $data = $response->getBody()->{0};
        if (!$this->validateLogin($data, $name)) {
            throw new AuthException(AuthErrorReason::InvalidCredentials);
        }
        // Check groups
        if (!$this->isAuthorized($data)) {
            throw new AuthException(AuthErrorReason::Unauthorized);
        }
        // Save to session
        if ($this->session) {
            $this->session->set($data);
        }
        return $data;
    }
    /**
     * Checks if the user is authorized to access the application.
     *
     * Matches if member of contains required key.
     *
     * @return bool The result indicating whether the user is authorized.
     */
    private function isAuthorized($login)
    {
        //No group lock defined
        if (empty('AD_GROUPS')) {
            return true;
        }

        $memberOf = $this->parseMemberOf($login->memberof);

        if (array_key_exists('CN', $memberOf) && is_array($this->AD_GROUPS) && count($this->AD_GROUPS)) {
            foreach ($this->AD_GROUPS as $group) {
                if (in_array($group, $memberOf['CN'])) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * This PHP function parses a string representing group memberships and returns an associative array
     * with group names as keys and an array of corresponding values.
     * 
     * @param memberOf The `parseMemberOf` function is designed to parse a string
     * containing group memberships. The function splits the input string by commas and then further
     * splits each part by the equal sign to extract the key-value pairs.
     * 
     * @return array An associative array is being returned where the keys are extracted from the input string
     * `` and the values are arrays of corresponding values.
     */
    private function parseMemberOf($memberOf)
    {
        $groups = [];
        $parts = explode(',', $memberOf);
        foreach ($parts as $part) {
            $group = explode('=', $part);
            $key = $group[0];
            $value = $group[1];
            if (!isset($groups[$key])) {
                $groups[$key] = [];
            }
            $groups[$key][] = trim($value);
        }
        return $groups;
    }

    /**
     * Validates the login response data.
     *
     * This private method validates the login response data by checking if it is an object,
     * if it contains an error, and if the 'samaccountname' matches the provided username.
     * If the response data is not an object or contains an error, the validation fails.
     * If the 'samaccountname' matches the provided username, the validation succeeds.
     * If none of these conditions are met, the validation result is null.
     *
     * @param mixed $data The response data received from the authentication server.
     * @param string $username The username used for authentication.
     *
     * @return bool|null Returns true if the validation succeeds, false if it fails,
     *                   and null if the validation result is inconclusive.
     */
    private function validateLogin($data, $username)
    {
        if (!is_object($data)) {
            return false;
        }
        if (isset($data->samaccountname) && strtolower($data->samaccountname) == strtolower($username)) {
            return true;
        }
        return null;
    }
}
