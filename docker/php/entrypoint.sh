#!/usr/bin/env sh
set -e

role="${ELIXE_RUNTIME_ROLE:-app}"

if [ "$role" = "app" ]; then
    if [ ! -f .env ]; then
        cp .env.example .env
    fi

    composer install --no-interaction --prefer-dist

    if ! grep -q '^APP_KEY=base64:' .env; then
        php artisan key:generate --force
    fi

    if [ "${ELIXE_RUN_MIGRATIONS:-true}" = "true" ]; then
        php artisan migrate --force
    fi
else
    attempts=0
    while [ ! -f vendor/autoload.php ] && [ "$attempts" -lt 60 ]; do
        attempts=$((attempts + 1))
        sleep 1
    done

    if [ ! -f vendor/autoload.php ]; then
        echo "Composer dependencies were not initialized by the app service." >&2
        exit 1
    fi
fi

exec "$@"
