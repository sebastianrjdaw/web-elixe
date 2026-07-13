# Docker

Development starts with:

```bash
docker compose up --build
```

Useful commands:

```bash
docker compose exec app composer install
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan elixe:create-admin admin@elixe.es
docker compose exec app php artisan xibo:test-connection
docker compose exec app php artisan xibo:sync-displays
docker compose exec app php artisan test
docker compose exec node npm run build
```

Services:

- `app`: PHP-FPM/Laravel.
- `nginx`: public HTTP entrypoint.
- `mysql`: application database.
- `redis`: cache and queues.
- `queue`: Laravel queue worker.
- `scheduler`: Laravel scheduler loop.
- `node`: Vite dev server.
- `mailpit`: local email inbox.

VPS deployment should reuse Docker Compose with production env values, persistent MySQL volumes, Redis, queue worker, scheduler and an HTTPS reverse proxy.
