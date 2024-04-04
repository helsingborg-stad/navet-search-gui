<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use NavetSearch\Helper\Config;

final class ConfigTest extends TestCase
{
    private $config;

    protected function setUp(): void
    {
        $this->config = new Config(array(
            "ENCRYPT_VECTOR" => "ABCDEFGHIJKLMNOP",
            "TEST_KEY_1" => "ABCDEF"
        ));
    }
    public function testReturnsValueOfKnownKeySuccessfully(): void
    {
        // Make sure the values are equals
        $this->assertEquals($this->config->getValue("ENCRYPT_VECTOR"), "ABCDEFGHIJKLMNOP");
    }
    public function testReturnsNullForUnknownKey(): void
    {
        // Make sure the values are equals
        $this->assertEquals($this->config->getValue("TEST_KEY_1"), null);
    }
    public function testReturnsNullForUndefinedKey(): void
    {
        // Make sure the values are equals
        $this->assertEquals($this->config->getValue("MS_NAVET_AUTH"), null);
    }
    public function testReturnsDefaultForUndefinedKey(): void
    {
        // Make sure the values are equals
        $this->assertEquals($this->config->getValue("TEST_KEY_1", "DEFAULT"), "DEFAULT");
    }
}
