#!/usr/bin/env sh
set -eu

EMAIL="${1:-admin@elixe.es}"
PASSWORD="${2:-admin123456}"

docker compose exec app php artisan elixe:create-admin "$EMAIL" --password="$PASSWORD" --name="Admin Elixe"
docker compose exec app php artisan elixe:check-admin-login "$EMAIL" "$PASSWORD"
