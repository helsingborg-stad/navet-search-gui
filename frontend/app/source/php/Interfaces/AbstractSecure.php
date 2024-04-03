<?php

namespace NavetSearch\Interfaces;

interface AbstractSecure
{
    public function encrypt(mixed $data): string|false;
    public function decrypt(string $data): mixed;
}
