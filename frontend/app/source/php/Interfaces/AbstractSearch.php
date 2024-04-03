<?php

namespace NavetSearch\Interfaces;

interface AbstractSearch
{
    public function find(string $pnr): array;
}
