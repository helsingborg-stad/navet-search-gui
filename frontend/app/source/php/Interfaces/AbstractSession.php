<?php

declare(strict_types=1);

namespace NavetSearch\Interfaces;

interface AbstractSession
{
    public function getSession(): mixed;
    public function setSession(mixed $data): bool;
    public function endSession(): void;
    public function isValidSession(): bool;
    public function getAccountName(): string|false;
    public function getSessionName(): string;
    public function getSessionExpiration(): int;
}
