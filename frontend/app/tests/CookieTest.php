<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use \NavetSearch\Helper\MemoryCookie;

final class CookieTest extends TestCase
{
    const name = 'dummy';

    public function testReturnsNullWhenNotSet(): void
    {
        $cookie = new MemoryCookie();
        // Make sure the values are equals
        $this->assertEquals($cookie->getCookie(self::name), null);
    }
    public function testReturnsValueWhenSetWithOptions(): void
    {
        $cookie = new MemoryCookie();
        $cookie->setCookie(self::name, 'data', [
            "expires" => 100
        ]);

        // Make sure the values are equals
        $this->assertEquals($cookie->getCookie(self::name), 'data');
    }
    public function testRemovesValueWhenSetWithoutOptions(): void
    {
        $cookie = new MemoryCookie();
        $cookie->setCookie(self::name, 'data', array());
        $cookie->setCookie(self::name);

        // Make sure the values are equals
        $this->assertEquals($cookie->getCookie(self::name), null);
    }
    public function testDoesNothingWhenSetWithoutOptions(): void
    {
        $cookie = new MemoryCookie();
        $cookie->setCookie(self::name);

        // Make sure the values are equals
        $this->assertEquals($cookie->getCookie(self::name), null);
    }
    public function testReturnsServerNameOption(): void
    {
        $cookie = new MemoryCookie();
        $server = $cookie->getServer();

        // Make sure the values are equals
        $this->assertEquals($server["SERVER_NAME"], "Memory");
    }
    public function testReturnsHttpsOption(): void
    {
        $cookie = new MemoryCookie();
        $server = $cookie->getServer();

        // Make sure the values are equals
        $this->assertEquals($server["HTTPS"], false);
    }
}
