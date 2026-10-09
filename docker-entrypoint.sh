#!/usr/bin/env bash
set -euo pipefail

cd /var/www

if [ "${APP_ENV:-local}" != "production" ] && [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist
fi

if [ -z "${APP_KEY:-}" ]; then
    php artisan key:generate --no-interaction --force
fi

php artisan storage:link --no-interaction 2>/dev/null || true

chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force --no-interaction
fi

if [ "${APP_ENV:-local}" = "production" ]; then
    php artisan config:cache --no-interaction
    php artisan route:cache --no-interaction
    php artisan view:cache --no-interaction
fi

exec "$@"
