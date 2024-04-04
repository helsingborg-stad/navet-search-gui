<?php

declare(strict_types=1);

namespace NavetSearch\Interfaces;

interface AbstractAuth
{
    public function authenticate(string $name, string $password): object;
}
