<?php

declare(strict_types=1);

namespace NavetSearch\Helper;

use NavetSearch\Interfaces\AbstractRequest;
use NavetSearch\Interfaces\AbstractResponse;

class Request implements AbstractRequest
{
    public function __construct(private bool $debug = false)
    {
    }

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
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $curlErrorNumber = curl_errno($ch);
        $curlError = curl_error($ch);

        curl_close($ch);

        if ($json === false) {
            $this->logDebug($method, $url, $statusCode ?: 502, $contentType, $curlErrorNumber, $curlError, null, false);
            return $this->createResponse($statusCode ?: 502, null);
        }

        $response = json_decode($json);
        $malformedResponse = json_last_error() !== JSON_ERROR_NONE || (!is_object($response) && !is_array($response));
        if (is_array($response)) {
            // The AD API returns successful users as a JSON list. Response keeps
            // object content, so retain its numeric keys as object properties.
            $response = (object) $response;
        }
        $this->logDebug($method, $url, $statusCode, $contentType, $curlErrorNumber, $curlError, $response, $malformedResponse);

        return $this->createResponse($statusCode, $response, $malformedResponse);
    }

    /**
     * Emit safe request diagnostics when DEBUG is enabled. Never log request data:
     * authentication requests contain passwords and successful API responses may
     * contain personal data.
     */
    private function logDebug(
        string $method,
        string $url,
        int $statusCode,
        string|false $contentType,
        int $curlErrorNumber,
        string $curlError,
        mixed $response,
        bool $malformedResponse
    ): void {
        if (!$this->debug) {
            return;
        }

        $errorMessage = null;
        if (is_object($response) && isset($response->error->message) && is_string($response->error->message)) {
            $errorMessage = mb_substr($response->error->message, 0, 500);
        }

        file_put_contents('php://stderr', 'navet curl debug ' . json_encode([
            'method' => $method,
            'url' => preg_replace('/\?.*/', '', $url),
            'http_status' => $statusCode,
            'content_type' => $contentType,
            'curl_errno' => $curlErrorNumber,
            'curl_error' => $curlError ?: null,
            'json_valid' => !$malformedResponse,
            'json_error' => $malformedResponse ? json_last_error_msg() : null,
            'api_error' => $errorMessage,
        ], JSON_UNESCAPED_SLASHES) . PHP_EOL);
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
