<?php

declare(strict_types=1);

namespace NavetSearch\Helper;

use NavetSearch\Interfaces\AbstractCookie;

/**
 * Wrapper class to allow cookie management during tests
 */
class MemoryCookie implements AbstractCookie
{
    private $values = [];

    public function set(string $key, mixed $data = "", mixed $options = null): bool
    {
        // Remove "cookie" if options is missing. This simulates the default
        // behaviour of the real "cookie" class
        if (!isset($options)) {
            unset($this->values[$key]);
        }
        $this->values[$key] = $data;
        return true;
    }
    public function get(string $key): string|null
    {
        return isset($this->values[$key]) ? $this->values[$key] : null;
    }
}
