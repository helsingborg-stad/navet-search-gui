<?php

declare(strict_types=1);

namespace NavetSearch\Interfaces;

interface AbstractConfig
{
    public function getValue(string $key, mixed $default = null): mixed;
}
