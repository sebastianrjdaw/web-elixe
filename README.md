<<<<<<< HEAD
# web-elixe
Landing page ecommerce con integracion a xibo
=======
# Elixe Ads Platform

Aplicacion Laravel + React/Inertia para captar locales y anunciantes, mostrar pantallas sincronizadas desde Xibo y enviar leads por email.

## Requisito local

Solo Docker con Compose. No hace falta instalar PHP, Composer, Node, MySQL ni Redis en la maquina.

## Arranque

```bash
docker compose up --build
```

Servicios:

- Web: http://localhost:8080
- Vite: http://localhost:5173
- Mailpit: http://localhost:8025
- MySQL expuesto en localhost:3307
- Redis expuesto en localhost:6379

## Comandos utiles

```bash
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
docker compose exec app php artisan elixe:create-admin admin@elixe.es
docker compose exec app php artisan xibo:test-connection
docker compose exec app php artisan xibo:sync-displays
docker compose exec app php artisan test
docker compose exec node npm run build
```

Las dependencias PHP viven en el volumen Docker `app-vendor` y las dependencias Node en `node-modules`; no se instalan en el host.

Rutas MVP principales:

- Home: http://localhost:8080
- Red de pantallas: http://localhost:8080/red-de-pantallas
- Asesoramiento: http://localhost:8080/asesoramiento
- Admin: http://localhost:8080/admin

Usuario admin local creado para desarrollo en este entorno:

```text
admin@elixe.es
admin123456
```

No uses esa contraseña en producción.

Diagnostico rapido:

```bash
docker compose exec app php artisan elixe:debug-env
docker compose exec app php artisan elixe:check-admin-login admin@elixe.es admin123456
docker compose exec app php artisan xibo:test-connection
./scripts/diagnose.sh --json
```

## Variables Xibo

Configura estas claves en `.env` antes de sincronizar:

```env
XIBO_CMS_URL=https://cms.elixe.es
XIBO_BASE_URL="${XIBO_CMS_URL}/api"
XIBO_CLIENT_ID=
XIBO_CLIENT_SECRET=
```
>>>>>>> 80705ff (Initial commit)
