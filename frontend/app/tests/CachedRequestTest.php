<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use NavetSearch\Helper\CachableRequest;
use NavetSearch\Helper\Config;
use NavetSearch\Helper\MemoryCache;
use NavetSearch\Helper\Response;
use NavetSearch\Helper\Secure;
use NavetSearch\Interfaces\AbstractRequest;

final class CachedRequestTest extends TestCase
{
    public function testReturnValueFromCache(): void
    {
        // Mock Requestclass
        $request = $this->createConfiguredMock(
            AbstractRequest::class,
            [
                'get' => new Response(200, null, null),
                'post' => new Response(200, null, (object)[
                    "dummy" => "value"
                ]),
            ],
        );
        // Create a memory cache with encryption
        $cache = new MemoryCache(new Secure(new Config()));

        // Create an instance of CachableRequest
        $cachedRequest = new CachableRequest($cache, $request);

        // Issue a request
        $response = $cachedRequest->post('url');

        // Expect the response to be fetchable from cache
        $this->assertEquals($cache->get($response->getHash())->dummy, "value");
    }
    public function testDontCacheErrorResponses(): void
    {
        $request = $this->createConfiguredMock(
            AbstractRequest::class,
            [
                'get' => new Response(401, null, null),
                'post' => new Response(401, null, (object)[
                    "dummy" => "value"
                ]),
            ],
        );
        $cache = new MemoryCache(new Secure(new Config()));

        $cachedRequest = new CachableRequest($cache, $request);

        $response = $cachedRequest->post('url');

        // Expect response to not be cached
        $this->assertEquals($cache->get($response->getHash()), null);
    }
}
