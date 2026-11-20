#!/bin/sh
set -eu

frankenphp php-cli /app/app/validate-config.php
exec docker-php-entrypoint "$@"
