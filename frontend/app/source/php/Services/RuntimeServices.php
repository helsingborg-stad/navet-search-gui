<?php

namespace NavetSearch\Services;

use NavetSearch\Helper\RedisCache;
use NavetSearch\Helper\Secure;
use NavetSearch\Helper\Session;
use NavetSearch\Helper\Auth;
use NavetSearch\Helper\MemoryCache;
use NavetSearch\Helper\Request;
use NavetSearch\Helper\Config;
use NavetSearch\Helper\Search;
use NavetSearch\Interfaces\AbstractCache;
use NavetSearch\Interfaces\AbstractRequest;
use NavetSearch\Interfaces\AbstractAuth;
use NavetSearch\Interfaces\AbstractSecure;
use \NavetSearch\Interfaces\AbstractServices;
use NavetSearch\Interfaces\AbstractSession;
use NavetSearch\Interfaces\AbstractConfig;
use NavetSearch\Interfaces\AbstractSearch;

class RuntimeServices implements AbstractServices
{
    private AbstractRequest $request;
    private AbstractCache $cache;
    private AbstractAuth $auth;
    private AbstractSearch $search;
    private AbstractSecure $secure;
    private AbstractSession $session;
    private AbstractConfig $config;

    public function __construct($config)
    {
        $this->config = new Config($config);
        $this->secure = new Secure($this->config);
        $this->session = new Session($this->secure);

        if ($this->config->get('PREDIS')) {
            $this->cache = new RedisCache($this->config, $this->secure);
        } else {
            echo "Using MemoryCache";
            $this->cache = new MemoryCache($this->secure);
        }

        $this->request = new Request($this->cache);
        $this->auth = new Auth($this->config, $this->request, $this->session);
        $this->search = new Search($this->config, $this->request, $this->session);
    }

    public function getRequestService(): Request
    {
        return $this->request;
    }
    public function getCacheService(): AbstractCache
    {
        return $this->cache;
    }
    public function getSessionService(): AbstractSession
    {
        return $this->session;
    }
    public function getAuthService(): AbstractAuth
    {
        return $this->auth;
    }
    public function getSecureService(): AbstractSecure
    {
        return $this->secure;
    }
    public function getConfigService(): AbstractConfig
    {
        return $this->config;
    }
    public function getSearchService(): AbstractSearch
    {
        return $this->search;
    }
}
