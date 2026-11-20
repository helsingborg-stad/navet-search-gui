FROM dunglas/frankenphp:1-php8.2-bookworm

WORKDIR /app

# Navet's front controller and assets live in app/.  FrankenPHP serves those
# files directly, while Caddy routes application paths to app/index.php.
COPY . /app
COPY Caddyfile /etc/frankenphp/Caddyfile

# TLS terminates at ingress-nginx; bind an unprivileged HTTP port in the pod.
EXPOSE 8080
