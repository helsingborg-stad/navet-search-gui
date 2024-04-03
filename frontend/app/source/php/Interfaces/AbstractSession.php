<?php

namespace NavetSearch\Interfaces;

interface AbstractSession
{
    public function get(): mixed;
    public function set(mixed $data): bool;
    public function end(): void;
    public function isValid(): bool;
    public function getAccountName(): string;
}
