<?php

namespace NavetSearch\Interfaces;

interface AbstractRequest
{
    public function get(string $url, $queryParams = [], $headers = []): AbstractResponse;
    public function post(string $url, $data = [], $headers = []): AbstractResponse;
}
