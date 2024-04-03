<?php

namespace NavetSearch\Helper;

use \Predis\Client as PredisClient;
use \NavetSearch\Interfaces\AbstractCache;
use \NavetSearch\Interfaces\AbstractConfig;
use \NavetSearch\Interfaces\AbstractSecure;

class RedisCache implements AbstractCache
{
    private $cache = null;
    private $secure = null;

    public function __construct(AbstractConfig $config, AbstractSecure $secure = null)
    {
        $this->secure = $secure;
        $this->cache = new PredisClient($config->get("PREDIS"));
    }
    public function __destruct()
    {
        // Cleanup
        $this->cache->quit();
    }

    public function set(string $key, mixed $data, int $ttl = 300): void
    {
        // Hash key
        $key = $this->hashKey($key);

        // Encrypt data
        if ($this->secure) {
            $data = $this->secure->encrypt($data);
        }
        // Set (encrypted) data
        $this->cache->set($key, $data, null, $ttl);
    }

    public function get(string $key): mixed
    {
        // Hash key
        $key = $this->hashKey($key);
        $data = $this->cache->get($key);

        // Decrypt
        if ($this->secure) {
            return $this->secure->decrypt($data);
        }
        // Return (decrypted) data
        return json_decode($data);
    }
    private function hashKey(string $key): string
    {
        return "data:" . md5($key);
    }
}
