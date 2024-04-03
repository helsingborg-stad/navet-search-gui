<?php

namespace NavetSearch\Helper;

use NavetSearch\Interfaces\AbstractCache;
use NavetSearch\Interfaces\AbstractSecure;

class MemoryCache implements AbstractCache
{
    private $cache = null;
    private $secure = null;

    public function __construct(AbstractSecure $secure = null)
    {
        $this->secure = $secure;
        $this->cache = array();
    }
    public function set(string $key, mixed $data, int $ttl = 300): void
    {
        // Hash key
        $key = $this->hashKey($key);

        // Encrypt data
        if ($this->secure) {
            $data = $this->secure->encrypt($data);
        } else {
            if (!is_string($data)) {
                $data = json_encode($data);
            }
        }
        // Push to array
        $this->cache += [$key => $data];
    }

    public function get(string $key): mixed
    {
        // Get from cache
        $key = $this->hashKey($key);

        if (isset($this->cache[$key])) {
            $data = $this->cache[$key];
            // Decrypt
            if ($this->secure) {
                return $this->secure->decrypt($data);
            }
            // Return (decrypted) data
            return json_decode($data);
        }
        return null;
    }
    private function hashKey(string $key): string
    {
        return "data:" . md5($key);
    }
}
