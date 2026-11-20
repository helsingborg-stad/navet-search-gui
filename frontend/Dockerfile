FROM dunglas/frankenphp:1-php8.2-bookworm

WORKDIR /app

# Navet's front controller and assets live in app/.  FrankenPHP serves those
# files directly, while Caddy routes application paths to app/index.php.
COPY . /app
COPY Caddyfile /etc/frankenphp/Caddyfile
COPY --chmod=755 docker-entrypoint.sh /usr/local/bin/navet-entrypoint

# TLS terminates at ingress-nginx; bind an unprivileged HTTP port in the pod.
EXPOSE 8080

ENTRYPOINT ["/usr/local/bin/navet-entrypoint"]
CMD ["--config", "/etc/frankenphp/Caddyfile", "--adapter", "caddyfile"]
