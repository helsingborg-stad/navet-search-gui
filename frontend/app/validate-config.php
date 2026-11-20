<?php

declare(strict_types=1);

const CONFIG_FILE = __DIR__ . '/../config.json';
const REQUIRED_ENDPOINTS = ['MS_AUTH', 'MS_NAVET'];

function configurationError(string $message): never
{
    fwrite(STDOUT, "navet configuration error: {$message}\n");
    exit(1);
}

if (!is_readable(CONFIG_FILE)) {
    configurationError('config.json is missing or unreadable');
}

$contents = file_get_contents(CONFIG_FILE);
if ($contents === false) {
    configurationError('config.json could not be read');
}

try {
    $config = json_decode(
        $contents,
        true,
        flags: JSON_THROW_ON_ERROR
    );
} catch (JsonException) {
    configurationError('config.json contains invalid JSON');
}

if (!is_array($config)) {
    configurationError('config.json must contain an object');
}

foreach (REQUIRED_ENDPOINTS as $key) {
    $endpoint = $config[$key] ?? null;
    $parts = is_string($endpoint) ? parse_url($endpoint) : false;

    if (
        !is_string($endpoint) ||
        trim($endpoint) === '' ||
        $parts === false ||
        !isset($parts['scheme'], $parts['host']) ||
        !in_array($parts['scheme'], ['http', 'https'], true) ||
        trim($parts['host']) === ''
    ) {
        configurationError("required endpoint {$key} is missing or has no HTTP(S) host");
    }
}

fwrite(STDOUT, "navet configuration validated\n");
