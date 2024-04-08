<?php

declare(strict_types=1);

namespace NavetSearch\Interfaces;

/**
 * Wrapper class to allow cookie management during tests
 */
interface AbstractCookie
{
    public function setCookie(string $key, mixed $data = "", mixed $options = null): bool;
    public function getCookie(string $key): string|null;
    public function getCookies(): array;
    public function getServer(): array;
}
