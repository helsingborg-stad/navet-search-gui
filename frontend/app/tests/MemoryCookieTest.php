<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use \NavetSearch\Helper\MemoryCookie;

final class MemoryCookieTest extends TestCase
{
    const name = 'dummy';

    public function testReturnsNullWhenNotSet(): void
    {
        $cookie = new MemoryCookie();
        // Make sure the values are equals
        $this->assertEquals($cookie->get(self::name), null);
    }
    public function testReturnsValueWhenSetWithOptions(): void
    {
        $cookie = new MemoryCookie();
        $cookie->set(self::name, 'data', array());

        // Make sure the values are equals
        $this->assertEquals($cookie->get(self::name), 'data');
    }
    public function testRemovesValueWhenSetWithoutOptions(): void
    {
        $cookie = new MemoryCookie();
        $cookie->set(self::name, 'data', array());
        $cookie->set(self::name);

        // Make sure the values are equals
        $this->assertEquals($cookie->get(self::name), null);
    }
    public function testDoesNothingWhenSetWithoutOptions(): void
    {
        $cookie = new MemoryCookie();
        $cookie->set(self::name);

        // Make sure the values are equals
        $this->assertEquals($cookie->get(self::name), null);
    }
}
