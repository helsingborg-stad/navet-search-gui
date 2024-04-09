<?php

declare(strict_types=1);

namespace NavetSearch\Models;

use JsonSerializable;
use NavetSearch\Helper\Format;
use NavetSearch\Interfaces\AbstractPropertyRegistrationHistory;
use NavetSearch\Helper\Sanitize;
use stdClass;

class PropertyRegistrationHistory implements AbstractPropertyRegistrationHistory, JsonSerializable
{
    private string $registrationDate;
    private string $countyCode;
    private string $municipalityCode;
    private ?string $parishCode;
    private string $designation;
    private ?string $key;
    private string $code;
    private string $description;

    public function __construct(object $history = new stdClass)
    {
        // Map from json
        $this->registrationDate = Sanitize::string(@$history->registrationDate);
        $this->countyCode = Sanitize::string(@$history->countyCode);
        $this->municipalityCode = Sanitize::string(@$history->municipalityCode);
        $this->parishCode = Sanitize::string(@$history->parishCode);

        $property = @$history->property ?? new stdClass;
        $this->designation = Sanitize::string(@$property->designation);
        $this->key = Sanitize::string(@$property->key);

        $type = @$history->type ?? new stdClass;
        $this->code = Sanitize::string(@$type->code);
        $this->description = Sanitize::string(@$type->description);
    }

    public function getRegistrationDate(): string
    {
        return Format::date($this->registrationDate);
    }
    public function getCountyCode(): string
    {
        return $this->countyCode;
    }
    public function getMunicipalityCode(): string
    {
        return $this->municipalityCode;
    }
    public function getParishCode(): string
    {
        return $this->parishCode;
    }
    public function getPropertyDesignation(): string
    {
        return $this->designation;
    }
    public function getPropertyKey(): string
    {
        return $this->key;
    }
    public function getTypeCode(): string
    {
        return $this->code;
    }
    public function getTypeDescription(): string
    {
        return $this->description;
    }

    public function jsonSerialize(): mixed
    {
        return [
            "registrationDate" => $this->registrationDate,
            "countyCode" => $this->countyCode,
            "municipalityCode" => $this->municipalityCode,
            "parishCode" => $this->parishCode,
            "property" => [
                "designation" => $this->designation,
                "key" => $this->key,
            ],
            "type" => [
                "code" => $this->code,
                "description" => $this->description,
            ]
        ];
    }
}
