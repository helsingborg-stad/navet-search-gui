<?php

declare(strict_types=1);

use NavetSearch\Models\CivilStatus;
use PHPUnit\Framework\TestCase;

final class CivilStatusTest extends TestCase
{
    private CivilStatus $status;

    protected function setUp(): void
    {;
        $this->status = new CivilStatus((object) [
            "code" => "code_value",
            "description" => "description_value",
            "date" => "date_value"
        ]);
    }

    public function testReturnsCivilStatusCodeSuccessfully(): void
    {
        $this->assertSame($this->status->getCivilStatusCode(), "code_value");
    }
    public function testReturnsCivilStatusDescriptionSuccessfully(): void
    {
        $this->assertSame($this->status->getCivilStatusDescription(), "description_value");
    }
    public function testReturnsCivilStatusDateSuccessfully(): void
    {
        $this->assertSame($this->status->getCivilStatusDate(), "date_value");
    }
    public function testReturnsDefaultValuesSuccessfully(): void
    {
        $status = new CivilStatus((object) []);

        $this->assertSame($status->getCivilStatusCode(), "");
        $this->assertSame($status->getCivilStatusDescription(), "");
        $this->assertSame($status->getCivilStatusDate(), "");
    }
    public function testSerializeJsonCorrectly(): void
    {
        $this->assertSame(json_encode($this->status), '{"code":"code_value","description":"description_value","date":"date_value"}');
    }
}
