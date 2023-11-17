<?php

namespace NavetSearch\Helper;

class Curl
{
    private $url;
    private $headers;
    private $response;
    private $cache;
    private $cacheEnabled;
    private $cacheTTL = 300; // Cache TTL in seconds (adjust as needed)

    public function __construct($url, $cacheEnabled = false)
    {
        $this->url = $url;
        $this->headers = [];
        $this->cacheEnabled = $cacheEnabled;

        if ($this->cacheEnabled) {
            $this->cache = new Redis();
            $this->cache->connect('127.0.0.1', 6379); // Replace with your Redis server details
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

        if ($this->cacheEnabled && $this->cache->exists($cacheKey)) {
            $this->response = json_decode($this->cache->get($cacheKey), true);
            return $this->response;
        }

        $ch = curl_init();

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $this->headers,
            CURLOPT_SSL_VERIFYPEER => false, // Adjust this based on your needs
        ];

        if ($method === 'POST' && !empty($data)) {
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
            $options[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
        }

        curl_setopt_array($ch, $options);

        $this->response = curl_exec($ch);

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $this->response = ['error' => curl_error($ch)];
        }

        curl_close($ch);

        if ($this->cacheEnabled) {
            $this->cache->setex($cacheKey, $this->cacheTTL, json_encode($this->response));
        }

        return $this->handleResponse($statusCode);
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

    private function handleResponse($statusCode)
    {
        if ($statusCode >= 400) {
            return ['error' => "Request failed with status code: $statusCode", 'response' => $this->response];
        }

        return json_decode($this->response, true);
    }

    public function __destruct()
    {
        if ($this->cacheEnabled) {
            $this->cache->close();
        }
    }
}


/*
// Example usage:
$request = new CurlRequest('https://api.example.com', true); // Enable caching

// Set headers using an array
$headers = ['Content-Type' => 'application/json', 'Authorization' => 'Bearer YOUR_ACCESS_TOKEN'];
$request->setHeaders($headers);

// GET request
$response = $request->get(['param1' => 'value1', 'param2' => 'value2']);

// POST request
$data = ['key' => 'value'];
$response = $request->post($data);

var_dump($response);

*/ 






/*

// Example usage:
$request = new CurlRequest('https://api.example.com', true); // Enable caching

// GET request
$response = $request->get(['param1' => 'value1', 'param2' => 'value2']);

// POST request
$data = ['key' => 'value'];
$response = $request->post($data);

var_dump($response);







class Curl
{
    public $response = false;
    public $isValid = false;
    public $errorMessage = false;
    public $oAuth = false;

    public function __construct($type, $url, $data = null, $contentType = 'json', $headers = null)
    {
        $arguments = $this->setCommonOptions(
            $type, 
            $url, 
            $data, 
            $contentType, 
            $headers
        );
        
        switch (strtoupper($type)) {
            case 'GET':
                $this->setGetOptions($arguments, $data);
                break;
            case 'POST':
                $this->setPostOptions($arguments, $data, $contentType);
                break;
        }

        $this->executeCurl($arguments);

        $this->processResponse();
    }

    private function setCommonOptions($type, $url, $data, $contentType, $headers)
    {
        $options = array(
            CURLOPT_RETURNTRANSFER      => true,
            CURLOPT_HEADER              => false,
            CURLOPT_FOLLOWLOCATION      => true,
            CURLOPT_CONNECTTIMEOUT_MS   => 2000,
            CURLOPT_URL                 => $url,
        );

        if ($headers && $headers = $this->convertHeaderArray($headers)) {
            $options[CURLOPT_HTTPHEADER] = $headers;
        }

        return $options;
    }

    private function setGetOptions(&$arguments, $data)
    {
        if (is_array($data) && !empty($data)) {
            $arguments[CURLOPT_URL] .= '?' . http_build_query($data);
        }
    }

    private function setPostOptions(&$arguments, $data, $contentType)
    {
        $arguments[CURLOPT_POST] = 1;
        $arguments[CURLOPT_REFERER] = '';

        if ($contentType === 'json' || $contentType === 'jsonp') {
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
        } else {
            $options[CURLOPT_POSTFIELDS] = http_build_query($data);
        }
    }

    private function executeCurl($arguments)
    {
        $ch = curl_init();
        curl_setopt_array($ch, $arguments);
        $response = curl_exec($ch);
        curl_close($ch);

        var_dump($response);

        $this->response = json_decode($response);
    }

    private function processResponse()
    {
        if (is_object($this->response) && !empty($this->response)) {
            $this->isValid = !isset($this->response->errors) && $this->response->status == 200;
        } else {
            $this->isValid = false;
        }
    }

    private function convertHeaderArray($header)
        return is_array($header) && !empty($header) ? array_map(fn($key, $value) => "$key: $value", array_keys($header), $header) : false;
    }
}
*/ 