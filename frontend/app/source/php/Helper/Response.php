<?php

declare(strict_types=1);

namespace NavetSearch\Helper;

use NavetSearch\Interfaces\AbstractResponse;

class Response implements AbstractResponse
{
    protected int $status;
    protected ?string $hash;
    protected ?object $content;
    protected bool $malformedResponse;

    public function __construct(int $status, ?string $hash, ?object $content, bool $malformedResponse = false)
    {
        $this->status = $status;
        $this->hash = $hash;
        $this->content = $content;
        $this->malformedResponse = $malformedResponse;
    }
    public function getStatusCode(): int
    {
        return $this->status;
    }
    public function getHash(): ?string
    {
        return $this->hash;
    }
    public function getContent(): ?object
    {
        return $this->content;
    }
    public function isErrorResponse(): bool
    {
        return $this->status >= 400;
    }
    public function isMalformedResponse(): bool
    {
        return $this->malformedResponse;
    }
}
