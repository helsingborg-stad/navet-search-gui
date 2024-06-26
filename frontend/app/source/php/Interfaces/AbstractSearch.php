<?php

declare(strict_types=1);

namespace NavetSearch\Interfaces;

interface AbstractSearch
{
    public function findPerson(string $pnr): array;
    public function getApiKey(): string;
    public function getEndpoint(): string;
}
