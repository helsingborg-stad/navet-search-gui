<?php

namespace NavetSearch\Interfaces;

interface AbstractAuth
{
    public function authenticate(string $name, string $password): object;
}
