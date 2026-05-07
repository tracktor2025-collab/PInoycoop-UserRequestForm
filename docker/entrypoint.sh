#!/bin/sh
set -e

mkdir -p \
  storage/framework/cache/data \
  storage/framework/sessions \
  storage/framework/testing \
  storage/framework/views \
  storage/logs \
  bootstrap/cache

if [ ! -f .env ] && [ -f .env.example ]; then
  cp .env.example .env
fi

if [ -f .env ] && ! grep -q '^APP_KEY=base64:' .env; then
  php artisan key:generate --force
fi

php artisan storage:link >/dev/null 2>&1 || true
php artisan config:clear >/dev/null 2>&1 || true

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
  php artisan migrate --force
fi

exec "$@"
