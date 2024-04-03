<?php

namespace NavetSearch\Helper;

use NavetSearch\Interfaces\AbstractConfig;

class Config implements AbstractConfig
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $this->parse($config);
    }

    public function get(string $key): mixed
    {
        return $this->config[$key] ?? null;
    }
    /**
     * Configure the application environment variables.
     *
     * This function retrieves environment variables from the system or uses default values from the provided configuration.
     *
     * @param array $config An associative array containing values for environment variables.
     *
     * @return void
     */
    private function parse(array $config): array
    {
        //Get env vars
        $env = array(
            'MS_AUTH' => getenv('MS_AUTH'),
            'MS_NAVET' => getenv('MS_NAVET'),
            'MS_NAVET_AUTH' => getenv('MS_NAVET_AUTH'),
            'ENCRYPT_VECTOR' => getenv('ENCRYPT_VECTOR'),
            'ENCRYPT_KEY' => getenv('ENCRYPT_KEY'),
            'PREDIS' => getenv('PREDIS'),
            'DEBUG' => getenv('DEBUG'),
            'AD_GROUPS' => getenv('AD_GROUPS')
        );

        //Fallback to default
        foreach ($env as $key => $item) {
            if ($item === false) {
                if (isset($config[$key]) && is_object($config[$key])) {
                    $config[$key] = (array) $config[$key];
                }
                $env[$key] = $config[$key] ?? false;
            }
        }
        return $env;
    }
}
