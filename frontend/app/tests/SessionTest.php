<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use NavetSearch\Helper\Config;
use NavetSearch\Helper\MemoryCookie;
use NavetSearch\Helper\Secure;
use NavetSearch\Helper\Session;

final class SessionTest extends TestCase
{
    private $session;

    protected function setUp(): void
    {
        $config = new Config(array(
            "ENCRYPT_VECTOR" => "ABCDEFGHIJKLMNOP",
            "TEST_KEY_1" => "ABCDEF"
        ));
        $secure = new Secure($config);

        $this->session = new Session($config, $secure, new MemoryCookie());
    }
    public function testRetreiveKnownKeySuccessfully(): void
    {
        $this->session->set([
            "samaccountname" => "hardy"
        ]);
        $this->assertEquals($this->session->isValid(), true);
    }
    public function testConfigValuesAreRespected(): void
    {
        $config = new Config(array(
            "SESSION_COOKIE_NAME" => "SessionName",
            "SESSION_COOKIE_EXPIRES" => "100"
        ));
        $session = new Session($config, new Secure($config), new MemoryCookie());

        $this->assertEquals($session->getSessionName(), "SessionName");
        $this->assertEquals($session->getSessionExpiration(), 100);
    }
    public function testConfigHasDefaultValues(): void
    {
        $config = new Config(array());
        $session = new Session($config, new Secure($config), new MemoryCookie());

        $this->assertEquals($session->getSessionName(), "navet_auth_cookie");
        $this->assertEquals($session->getSessionExpiration(), 36000);
    }
}
