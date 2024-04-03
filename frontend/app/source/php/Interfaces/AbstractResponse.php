<?php

namespace NavetSearch\Interfaces;

interface AbstractResponse
{
    public function getStatus(): int;
    public function getBody(): object|null;
    public function isErrorResponse(): bool;
}
