#!/usr/bin/env bash
set -euo pipefail

cd /var/www

if [ ! -f .env ]; then
    cp .env.example .env
fi

if [ "${APP_ENV:-local}" != "production" ] && [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist
fi

app_key_from_env_file() {
    grep -E '^APP_KEY=' .env 2>/dev/null | cut -d= -f2- | tr -d " \t\r\n\""
}

if [ -z "$(app_key_from_env_file)" ]; then
    php artisan key:generate --no-interaction --force
fi

unset APP_KEY

mkdir -p storage/framework/{cache,sessions,views,testing} storage/logs bootstrap/cache

php artisan storage:link --no-interaction 2>/dev/null || true

chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache

if [ "${DOCKER_BOOTSTRAP_DB:-false}" = "true" ]; then
    php artisan migrate --force --no-interaction
    php artisan db:seed --force --no-interaction
fi

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force --no-interaction
fi

if [ "${APP_ENV:-local}" = "production" ]; then
    php artisan config:cache --no-interaction
    php artisan route:cache --no-interaction
    php artisan view:cache --no-interaction
fi

exec "$@"
