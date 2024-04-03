<?php

namespace NavetSearch\Interfaces;

interface AbstractCache
{
    public function get(string $key): mixed;
    public function set(string $key, mixed $data, int $ttl = 300): void;
}
