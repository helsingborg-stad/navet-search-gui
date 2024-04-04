<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use NavetSearch\Helper\Secure;
use NavetSearch\Helper\Config;

final class SecureTest extends TestCase
{
    private Secure $secure;

    protected function setUp(): void
    {
        $this->secure = new Secure(new Config([]));
    }
    public function testReturnObjectWhenDecryptAndEncryptArray(): void
    {
        $value = array(
            "data1" => 10,
            "data2" => "value"
        );
        $encrypt = $this->secure->encrypt($value);
        $decrypt = $this->secure->decrypt($encrypt);

        // Make sure the values are equals
        $this->assertEquals((array)$decrypt, $value);
    }
    public function testReturnNullWhenDecryptAndEncryptString(): void
    {
        $encrypt = $this->secure->encrypt("data");
        $decrypt = $this->secure->decrypt($encrypt);

        // Make sure the values are equals
        $this->assertEquals($decrypt, null);
    }
    public function testReturnObjectWhenDecryptAndEncryptJson(): void
    {
        $value = (object) array(
            "data1" => 10,
            "data2" => "value"
        );
        $encrypt = $this->secure->encrypt($value);
        $decrypt = $this->secure->decrypt($encrypt);

        // Make sure the values are equals
        $this->assertEquals($decrypt, $value);
    }
}
