<?php

declare(strict_types=1);

namespace NavetSearch\Interfaces;

interface AbstractAuth
{
    public function authenticate(string $name, string $password): object;
    public function getEndpoint(): string;
    public function getGroups(): string;
}
