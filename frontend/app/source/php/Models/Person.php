<?php

declare(strict_types=1);

namespace NavetSearch\Models;

use JsonSerializable;
use NavetSearch\Helper\Format;
use NavetSearch\Interfaces\AbstractPerson;
use NavetSearch\Helper\Sanitize;
use stdClass;

class Person implements AbstractPerson, JsonSerializable
{
    private ?string $deregistrationCode;
    private ?string $deregistrationReason;
    private ?string $deregistrationDate;
    private string $givenName;
    private string $familyName;
    private string $additionalName;
    private string $addressLocality;
    private string $postalCode;
    private string $streetAddress;
    private string $provinceCode;
    private string $municipalityCode;

    public function __construct(object $person = new stdClass)
    {
        // Map from json
        $this->deregistrationCode = Sanitize::string(@$person->deregistrationCode);
        $this->deregistrationReason = Sanitize::string(@$person->deregistrationReason);
        $this->deregistrationDate = Sanitize::string(@$person->deregistrationDate);
        $this->givenName = Sanitize::string(@$person->givenName);
        $this->familyName = Sanitize::string(@$person->familyName);
        $this->additionalName = Sanitize::string(@$person->additionalName);
        $address = @$person->address ?? new stdClass;
        $this->addressLocality = Sanitize::string(@$address->addressLocality);
        $this->postalCode = Sanitize::string(@$address->postalCode);
        $this->streetAddress = Sanitize::string(@$address->streetAddress);
        $this->provinceCode = Sanitize::string(@$address->provinceCode);
        $this->municipalityCode = Sanitize::string(@$address->municipalityCode);
    }
    public function isDeregistered(): bool
    {
        if (!empty($this->deregistrationCode)) {
            return true;
        }
        return false;
    }
    public function getDeregistrationCode(): string
    {
        return $this->deregistrationCode;
    }
    public function getDeregistrationReason(): string
    {
        return $this->deregistrationReason;
    }
    public function getDeregistrationDate(): string
    {
        return Format::date($this->deregistrationDate);
    }
    public function getGivenName(): string
    {
        return $this->givenName;
    }
    public function getFamilyName(): string
    {
        return $this->familyName;
    }
    public function getAdditionalName(): string
    {
        return $this->additionalName;
    }
    public function getAddressLocality(): string
    {
        return Format::capitalize($this->addressLocality);
    }
    public function getPostalCode(): string
    {
        return Format::postalCode($this->postalCode);
    }
    public function getStreetAddress(): string
    {
        return Format::capitalize($this->streetAddress);
    }
    public function getProvinceCode(): string
    {
        return $this->provinceCode;
    }
    public function getMunicipalityCode(): string
    {
        return $this->municipalityCode;
    }
    public function jsonSerialize(): mixed
    {
        return [
            "deregistrationCode" => $this->deregistrationCode,
            "deregistrationDate" => $this->deregistrationDate,
            "deregistrationReason" => $this->deregistrationReason,
            "givenName" => $this->givenName,
            "familyName" => $this->familyName,
            "additionalName" => $this->additionalName,
            "address" => [
                "addressLocality" => $this->addressLocality,
                "postalCode" => $this->postalCode,
                "streetAddress" => $this->streetAddress,
                "provinceCode" => $this->provinceCode,
                "municipalityCode" => $this->municipalityCode,
            ]
        ];
    }
}
