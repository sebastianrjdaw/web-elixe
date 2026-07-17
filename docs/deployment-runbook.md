# Despliegue y operación de Elixe Web

## Requisitos

- Docker Compose con Node 22 y PHP 8.4.1 o superior.
- MySQL, Redis, un worker de cola y una única instancia del scheduler.
- Dominio HTTPS y `APP_URL` definitivo.
- SMTP, Xibo y Turnstile configurados mediante secretos externos al repositorio.

Antes de producción deben sustituirse y aprobarse los textos legales desde el admin. El diagnóstico `/admin/diagnostics` avisa si detecta contenido provisional, `APP_DEBUG`, HTTP o Turnstile desactivado.

## Variables mínimas de producción

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://dominio.example
APP_TIMEZONE=Europe/Madrid
APP_LOCALE=es
SESSION_SECURE_COOKIE=true

QUEUE_CONNECTION=redis
CACHE_DRIVER=redis
SESSION_DRIVER=redis

MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME=Elixe

XIBO_CMS_URL=
XIBO_CLIENT_ID=
XIBO_CLIENT_SECRET=

TURNSTILE_ENABLED=true
TURNSTILE_SITE_KEY=
TURNSTILE_SECRET_KEY=
```

No se deben usar las contraseñas de ejemplo de MySQL fuera del entorno local.

## Secuencia de despliegue

```bash
docker compose build --pull app
docker compose run --rm node npm ci
docker compose run --rm node npm run check
docker compose run --rm app php artisan test
docker compose run --rm app ./vendor/bin/pint --test
docker compose up -d mysql redis app nginx queue scheduler
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force
docker compose exec app php artisan optimize
```

Los seeders solo crean registros ausentes: no sobrescriben contenido, ajustes o plantillas editados desde el admin.

## Smoke test

1. Abrir `/`, `/gl`, `/red-de-pantallas`, `/gl/red-de-pantallas` y `/sitemap.xml`.
2. Enviar una solicitud de prueba y confirmar una sola alta, email interno y confirmación al contacto.
3. Iniciar sesión como administrador, filtrar el lead, cambiar su estado y enviar una respuesta por plantilla.
4. Ejecutar `/admin/diagnostics` y resolver todos los avisos de producción.
5. Ejecutar una sincronización Xibo manual y revisar `/admin/sync-runs`.
6. Confirmar que worker y scheduler están activos y que solo existe un scheduler.

## Migración y rollback

La migración `2026_07_17_000001_professionalize_core_workflows` añade columnas sin eliminar datos existentes, genera ULID para pantallas actuales y concede `is_admin=true` únicamente a los usuarios ya existentes. Los usuarios nuevos no son administradores por defecto.

Antes de migrar en producción, realiza una copia de seguridad de MySQL. Si es imprescindible volver atrás:

```bash
docker compose exec app php artisan down
docker compose exec app php artisan migrate:rollback --step=1 --force
```

El rollback elimina historial, plantillas, tokens de envío, locale, IDs públicos y el indicador administrativo incorporados por la migración; por eso debe hacerse solo después de exportar esos datos. Para una regresión de aplicación sin pérdida de datos, es preferible restaurar la versión anterior del código y mantener la migración aplicada.

## Operación

```bash
docker compose exec app php artisan xibo:test-connection
docker compose exec app php artisan xibo:sync-displays
docker compose exec app php artisan queue:failed
docker compose exec app php artisan schedule:list
docker compose logs --tail=200 app queue scheduler
```

La sincronización Xibo usa un bloqueo distribuido, pagina hasta completar resultados, elimina etiquetas obsoletas y oculta pantallas retiradas de Xibo. Nunca escribe en Xibo.
