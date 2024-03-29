<?php

namespace NavetSearch\Helper;

use \Predis\Client as PredisClient;
use \NavetSearch\Helper\Secure as Secure;

class Curl
{
    public static $responses = [];

    private $url;
    private $headers;
    private $response;
    private $cache;
    private $cacheEnabled;
    private $cacheTTL = 300; // Cache TTL in seconds (adjust as needed)
    public  $errors = [];

    public function __construct($url, $cacheEnabled = true)
    {
        $this->url = $url;
        $this->headers = [];
        $this->cacheEnabled = $cacheEnabled;

        if ($this->cacheEnabled && PREDIS) {
            try {
                $this->cache = new PredisClient(PREDIS);
            } catch(\Predis\Connection\ConnectionException $e) {
                echo $e->getMessage();
            }
        } else {
            $this->cacheEnabled = false;
        }
    }

    public function setHeader($key, $value)
    {
        $this->headers[] = "$key: $value";
    }

    public function setHeaders($headers)
    {
        if (is_array($headers) && !empty($headers)) {
            $convertedHeaders = $this->convertHeaderArray($headers);
            if ($convertedHeaders) {
                $this->headers = array_merge($this->headers, $convertedHeaders);
            }
        }
    }

    public function get($queryParams = [])
    {
        $url = $this->url;

        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        return $this->sendRequest('GET', $url);
    }

    public function post($data = [])
    {
        return $this->sendRequest('POST', $this->url, $data);
    }

    private function sendRequest($method, $url, $data = null)
    {
        $cacheKey = $this->generateCacheKey($method, $url, $data);
        if ($this->cacheEnabled && $cached = $this->cache->get($cacheKey)) {
            $this->response = Secure::decrypt($cached);
            return $this->response;
        }

        $ch = curl_init();

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $this->headers,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 3000,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4 //User authentication api misconfigured. Remove when ipv6 is working.
        ];

        if ($method === 'POST' && !empty($data)) {
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
            $options[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
        }

        curl_setopt_array($ch, $options);

        $this->response = json_decode(curl_exec($ch));

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($this->cacheEnabled) {
            $this->cache->set(
                $cacheKey, 
                Secure::encrypt($this->response)
            );
            $this->cache->expire(
                $cacheKey, 
                $this->cacheTTL
            );
        }

        return $this->handleResponse($statusCode, $this->response);
    }

    private function generateCacheKey($method, $url, $data)
    {
        $key = md5($method . $url . json_encode($data));
        return "curl_request:$key";
    }

    private function convertHeaderArray($header)
    {
        return is_array($header) && !empty($header) ?
            array_map(fn($key, $value) => "$key: $value", array_keys($header), $header) : false;
    }

    private function handleResponse($statusCode, $response)
    {
        if ($statusCode >= 400) {
            return [
                'staus' => $statusCode,
                'error' => "Request failed with status code: $statusCode", 
                'response' => $this->response
            ];
        }
        return self::$responses[] = $this->response;
    }

    public function __destruct()
    {
        if ($this->cacheEnabled) {
            $this->cache->disconnect();
        }
    }
}