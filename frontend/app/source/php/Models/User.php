<?php

declare(strict_types=1);

namespace NavetSearch\Models;

use JsonSerializable;
use NavetSearch\Interfaces\AbstractUser;
use stdClass;

class User implements AbstractUser, JsonSerializable
{
    private string $account = "";
    private string $groups = "";
    private string $company = "";
    private string $displayname = "";
    private string $sn = "";
    private string $mail = "";

    public function __construct(object $user = new stdClass)
    {
        if (is_object($user)) {
            // Map from json
            $this->account = $user->samaccountname ?? "";
            $this->groups = $user->memberof ?? "";
            $this->company = $user->company ?? "";
            $this->displayname = $user->displayname ?? "";
            $this->sn = $user->sn ?? "";
            $this->mail = $user->mail ?? "";
        }
    }
    public function getAccountName(): string
    {
        return $this->account;
    }
    public function getCompanyName(): string
    {
        return $this->company;
    }
    public function getDisplayName(): string
    {
        return $this->displayname;
    }
    public function getLastName(): string
    {
        return $this->sn;
    }
    public function getMailAddress(): string
    {
        return $this->mail;
    }
    public function getGroups(): array
    {
        $groups = [];
        if (!empty($this->groups)) {
            $parts = explode(',', $this->groups);
            foreach ($parts as $part) {
                $group = explode('=', $part);
                $key = trim($group[0]);
                $value = trim($group[1]);
                if (!isset($groups[$key])) {
                    $groups[$key] = [];
                }
                if(in_array($value, $groups[$key])){
                    continue;
                }
                $groups[$key][] = $value;
            }
        }
        return $groups;
    }
    public function format(): object
    {
        $userObject = [
            'firstname' => '',
            'lastname' => '',
            'administration' => $this->company
        ];
        if (isset($this->sn) && !empty($this->sn)) {
            $names = explode(" - ", $this->displayname, 2);
            $userObject['firstname'] = trim(str_replace($this->sn, "", $names[0]));
            $userObject['lastname'] = $this->sn;
        } elseif (isset($this->mail) && strpos($this->mail, ".")) {
            list($userObject['firstname'], $userObject['lastname']) = explode(".", strtok($this->mail, "@"), 2);
        } else {
            $tempData = trim(explode(" - ", $this->displayname, 2)[0]);

            if (!empty($tempData)) {
                $tempData = explode(" ", $tempData);
                $userObject['lastname'] = $tempData[0];
                unset($tempData[0]);
                $userObject['firstname'] = implode(" ", $tempData);
            }
        }

        // Uppercase first letters
        $userObject['firstname'] = ucfirst($userObject['firstname']);
        $userObject['lastname'] = ucfirst($userObject['lastname']);

        return (object) $userObject;
    }

    public function jsonSerialize(): mixed
    {
        return [
            "samaccountname" => $this->account,
            "memberof" => $this->groups,
            "company" => $this->company,
            "displayname" => $this->displayname,
            "sn" => $this->sn,
            "mail" => $this->mail,
        ];
    }
}
