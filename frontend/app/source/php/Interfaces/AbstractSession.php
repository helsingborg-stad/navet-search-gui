<?php

declare(strict_types=1);

namespace NavetSearch\Interfaces;

interface AbstractSession
{
    public function get(): mixed;
    public function set(mixed $data): bool;
    public function end(): void;
    public function isValid(): bool;
    public function getAccountName(): string|false;
}
