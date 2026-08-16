#!/bin/sh
set -eu

cd /app

mkdir -p \
    storage/app/public \
    storage/app/private \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    database

if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    db_file="${DB_DATABASE:-/app/database/database.sqlite}"

    case "$db_file" in
        /*) ;;
        *) db_file="/app/${db_file}" ;;
    esac

    mkdir -p "$(dirname "$db_file")"
    [ -f "$db_file" ] || touch "$db_file"
fi

chmod -R ug+rwx storage bootstrap/cache database

php artisan storage:link --force >/dev/null 2>&1 || true

if [ -n "${APP_KEY:-}" ]; then
    php artisan migrate --force --no-interaction
    php artisan optimize
fi

exec frankenphp run --config /etc/caddy/Caddyfile
