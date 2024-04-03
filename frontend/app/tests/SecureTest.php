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
        $config = new Config(array(
            "ENCRYPT_VECTOR" => "ABCDEFGHIJKLMNOP",
            "ENCRYPT_KEY" => "ABCDEFGHIJ"
        ));
        $this->secure = new Secure($config);
    }
    public function testDecryptAndEncryptArray(): void
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
    public function testDecryptAndEncryptNull(): void
    {
        $encrypt = $this->secure->encrypt(null);
        $decrypt = $this->secure->decrypt($encrypt);

        // Make sure the values are equals
        $this->assertEquals($decrypt, null);
    }
    public function testDecryptAndEncryptString(): void
    {
        $encrypt = $this->secure->encrypt("data");
        $decrypt = $this->secure->decrypt($encrypt);

        // Make sure the values are equals
        $this->assertEquals($decrypt, null);
    }
    public function testDecryptAndEncryptBoolean(): void
    {
        $encrypt = $this->secure->encrypt(true);
        $decrypt = $this->secure->decrypt($encrypt);

        // Make sure the values are equals
        $this->assertEquals($decrypt, true);
    }
    public function testDecryptAndEncryptJson(): void
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
