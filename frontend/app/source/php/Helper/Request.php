<?php

namespace NavetSearch\Helper;

use NavetSearch\Interfaces\AbstractRequest;
use NavetSearch\Interfaces\AbstractCache;
use NavetSearch\Interfaces\AbstractResponse;

class Request implements AbstractRequest
{
    private AbstractCache $cache;

    public function __construct(AbstractCache $cache = null)
    {
        $this->cache = $cache;
    }

    private function setHeaders($headers)
    {
        if (is_array($headers) && !empty($headers)) {
            return array_map(fn ($key, $value) => "$key: $value", array_keys($headers), $headers);
        }
        return [];
    }

    public function get($url, $queryParams = [], $headers = []): AbstractResponse
    {
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }
        return $this->sendRequest('GET', $url);
    }

    public function post($url, $data = [], $headers = []): AbstractResponse
    {
        return $this->sendRequest('POST', $url, $data, $headers);
    }

    private function sendRequest($method, $url, $data = null, $headers = null): AbstractResponse
    {
        // Format cache-key
        $key = $method . $url . json_encode($data);

        // Fetch from cache
        if ($this->cache && $cached = $this->cache->get($key)) {
            return new Response(200, $cached);
        }
        // Fetch from http
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
        $response = json_decode($json);

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        // Save to cache
        $this->cache && $this->cache->set($key, $response);
        return $this->handleResponse($statusCode, $response);
    }

    private function handleResponse($statusCode, $response)
    {
        if ($statusCode >= 400) {
            return new Response($statusCode, (object)[
                'status' => $statusCode,
                'error' => "Request failed with status code: $statusCode",
                'response' => $response
            ]);
        }
        return new Response(200, (object)$response);
    }
}
