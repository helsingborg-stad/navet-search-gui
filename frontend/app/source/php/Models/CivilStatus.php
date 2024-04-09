<?php

declare(strict_types=1);

namespace NavetSearch\Models;

use JsonSerializable;
use NavetSearch\Interfaces\AbstractCivilStatus;
use NavetSearch\Helper\Sanitize;
use stdClass;

class CivilStatus implements AbstractCivilStatus, JsonSerializable
{
    private string $code;
    private string $description;
    private string $date;

    public function __construct(object $status = new stdClass)
    {
        // Map from json
        $this->code = Sanitize::string(@$status->code);
        $this->description = Sanitize::string(@$status->description);
        $this->date = Sanitize::string(@$status->date);
    }

    public function getCivilStatusCode(): string
    {
        return $this->code;
    }
    public function getCivilStatusDescription(): string
    {
        return $this->description;
    }
    public function getCivilStatusDate(): string
    {
        return $this->date;
    }
    public function jsonSerialize(): mixed
    {
        return [
            "code" => $this->code,
            "description" => $this->description,
            "date" => $this->date,
        ];
    }
}
