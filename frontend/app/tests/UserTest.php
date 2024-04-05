<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use NavetSearch\Helper\User;

final class UserTest extends TestCase
{
    protected $user;

    protected function setUp(): void
    {
        $this->user = new User((object) [
            "samaccountname" => "samaccountname_value",
            "memberof" => "A=a,A=b,B=b,C=c,D=d",
            "sn" => "sn_value",
            "displayname" => "displayname_value",
            "company" => "company_value",
            "mail" => "mail_value",
        ]);
    }
    public function testReturnAccountNameSuccessfully(): void
    {
        $this->assertEquals($this->user->getAccountName(), "samaccountname_value");
    }
    public function testReturnLastNameSuccessfully(): void
    {
        $this->assertEquals($this->user->getLastName(), "sn_value");
    }
    public function testReturnDisplayNameSuccessfully(): void
    {
        $this->assertEquals($this->user->getDisplayName(), "displayname_value");
    }
    public function testReturnCompanyNameSuccessfully(): void
    {
        $this->assertEquals($this->user->getCompanyName(), "company_value");
    }
    public function testReturnMailAddressSuccessfully(): void
    {
        $this->assertEquals($this->user->getMailAddress(), "mail_value");
    }
    public function testReturnGroupsSuccessfully(): void
    {
        $this->assertEquals($this->user->getGroups(), [
            "A" => [0 => "a", 1 => "b"],
            "B" => ["b"],
            "C" => ["c"],
            "D" => ["d"],
        ]);
    }
    public function testFormatUserSuccessfully(): void
    {
        $this->assertEquals($this->user->format(), (object)[
            "firstname" => "Displayname_value",
            "lastname" => "Sn_value",
            "administration" => "company_value",
        ]);
    }
    public function testReturnDefaultValuesSuccessfully(): void
    {
        $user = new User((object) []);

        $this->assertEquals($user->getAccountName(), "");
        $this->assertEquals($user->getLastName(), "");
        $this->assertEquals($user->getDisplayName(), "");
        $this->assertEquals($user->getCompanyName(), "");
        $this->assertEquals($user->getMailAddress(), "");
        $this->assertEquals($user->getGroups(), []);
    }
}
