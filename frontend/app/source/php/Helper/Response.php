<?php

namespace NavetSearch\Helper;

use NavetSearch\Interfaces\AbstractResponse;

class Response implements AbstractResponse
{
    private int $status;
    private object|null $response;

    public function __construct(int $status, object|null $response)
    {
        $this->status = $status;
        $this->response = $response;
    }
    public function getStatus(): int
    {
        return $this->status;
    }
    public function getBody(): object|null
    {
        return $this->response;
    }
    public function isErrorResponse(): bool
    {
        return $this->status > 400;
    }
}
