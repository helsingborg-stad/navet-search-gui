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
    public function testRetreiveKnownKeySuccessfully(): void
    {
        // Make sure the values are equals
        $this->assertEquals($this->config->get("ENCRYPT_VECTOR"), "ABCDEFGHIJKLMNOP");
    }
    public function testRetreiveNullForUnknownKey(): void
    {
        // Make sure the values are equals
        $this->assertEquals($this->config->get("TEST_KEY_1"), null);
    }
    public function testRetreiveNullForUndefinedKey(): void
    {
        // Make sure the values are equals
        $this->assertEquals($this->config->get("MS_NAVET_AUTH"), null);
    }
    public function testRetreiveDefaultForUndefinedKey(): void
    {
        // Make sure the values are equals
        $this->assertEquals($this->config->get("TEST_KEY_1", "DEFAULT"), "DEFAULT");
    }
}
