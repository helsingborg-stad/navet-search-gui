<?php

declare(strict_types=1);

namespace NavetSearch\Interfaces;

interface AbstractResponse
{
    public function getStatusCode(): int;
    public function getContent(): ?object;
    public function getHash(): ?string;
    public function isErrorResponse(): bool;
}
