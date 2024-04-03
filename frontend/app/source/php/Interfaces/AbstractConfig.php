<?php

namespace NavetSearch\Interfaces;

interface AbstractConfig
{
    public function get(string $key, mixed $default = null): mixed;
}
