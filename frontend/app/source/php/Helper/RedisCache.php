<?php

declare(strict_types=1);

namespace NavetSearch\Helper;

use \Predis\Client as PredisClient;
use \NavetSearch\Interfaces\AbstractCache;
use \NavetSearch\Interfaces\AbstractConfig;
use \NavetSearch\Interfaces\AbstractSecure;
use \NavetSearch\Interfaces\AbstractResponse;

class RedisCache implements AbstractCache
{
    protected $cache = null;
    protected $secure = null;

    public function __construct(AbstractConfig $config, ?AbstractSecure $secure = null)
    {
        $this->secure = $secure;
        $this->cache = new PredisClient($config->getValue("PREDIS"));
    }

    public function __destruct()
    {
        // Cleanup
        $this->cache->quit();
    }

    public function set(AbstractResponse $response, int $ttl = 300): void
    {
        if ($key = $response->getHash()) {
            $content = $response->getContent();
            // Encrypt data
            if ($this->secure) {
                $content = $this->secure->encrypt($content);
            }
            // Set (encrypted) data
            $this->cache->set($key, $content, null, $ttl);
        }
    }

    public function get(string $key): mixed
    {
        $content = $this->cache->get($key);
        // Decrypt
        if ($content) {
            if ($this->secure) {
                return $this->secure->decrypt($content);
            }
            // Return (decrypted) data
            return json_decode($content);
        }
        return null;
    }
}
