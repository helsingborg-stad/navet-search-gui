<?php

declare(strict_types=1);

namespace NavetSearch\Interfaces;

interface AbstractCivilStatus
{
    public function getCivilStatusCode(): string;
    public function getCivilStatusDescription(): string;
    public function getCivilStatusDate(): string;
    public function jsonSerialize(): mixed;
}
