<?php

declare(strict_types=1);

namespace NavetSearch\Helper;

use NavetSearch\Interfaces\AbstractRequest;
use NavetSearch\Interfaces\AbstractResponse;

class Request implements AbstractRequest
{
    public function get(string $url, $queryParams = [], $headers = []): AbstractResponse
    {
        return $this->send('GET', $this->setQueryParams($url, $queryParams));
    }

    public function post(string $url, $data = [], $headers = []): AbstractResponse
    {
        return $this->send('POST', $url, $data, $headers);
    }

    protected function setHeaders($headers)
    {
        if (is_array($headers) && !empty($headers)) {
            return array_map(fn ($key, $value) => "$key: $value", array_keys($headers), $headers);
        }
        return [];
    }

    protected function setQueryParams(string $url, $queryParams = [])
    {
        if (!empty($queryParams)) {
            return $url .= '?' . http_build_query($queryParams);
        }
        return $url;
    }

    protected function send($method, $url, $data = null, $headers = null): AbstractResponse
    {
        $ch = curl_init();

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $this->setHeaders($headers),
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 3000,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4 //User authentication api misconfigured. Remove when ipv6 is working.
        ];

        if ($method === 'POST' && !empty($data)) {
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
            $options[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
        }

        curl_setopt_array($ch, $options);

        $json = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($json === false) {
            return $this->createResponse($statusCode ?: 502, null);
        }

        $response = json_decode($json);
        $malformedResponse = json_last_error() !== JSON_ERROR_NONE || !is_object($response);

        return $this->createResponse($statusCode, $response, $malformedResponse);
    }

    protected function createResponse(int $statusCode, mixed $content, bool $malformedResponse = false): AbstractResponse
    {
        if ($statusCode < 200 || $statusCode >= 300 || $malformedResponse) {
            $statusCode = $statusCode >= 200 && $statusCode < 300 ? 502 : $statusCode;
            return new Response($statusCode, null, (object)[
                'status' => $statusCode,
                'error' => $malformedResponse
                    ? 'Request returned malformed JSON'
                    : "Request failed with status code: $statusCode",
                'response' => (object) $content
            ], $malformedResponse);
        }
        return new Response(200, null, (object) $content);
    }
}
