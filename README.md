# Elixe Ads Platform

Aplicación Laravel + React/Inertia para captar locales y anunciantes, publicar la red de pantallas sincronizada desde Xibo y gestionar oportunidades comerciales.

## Desarrollo local

Solo se necesita Docker con Compose; PHP, Composer, Node, MySQL y Redis se ejecutan en contenedores.

```bash
docker compose up --build
```

Servicios disponibles:

- Web: http://localhost:8080
- Vite: http://localhost:5173
- Mailpit: http://localhost:8025
- MySQL: `localhost:3307`
- Redis: `localhost:6379`

El contenedor `app` instala Composer y ejecuta las migraciones una sola vez. Cola y scheduler esperan a que las dependencias estén listas. Antes de arrancar Vite, `node-init` prepara el volumen de dependencias; el servicio Node usa `LOCAL_UID` y `LOCAL_GID` (ambos `1000` por defecto) para no crear archivos del host propiedad de `root`.

## Puesta en marcha

```bash
docker compose exec app php artisan db:seed
docker compose exec app php artisan elixe:create-admin admin@elixe.es
```

El comando genera una contraseña temporal segura si no se indica `--password`. No hay credenciales predeterminadas en el repositorio.

## Verificación

```bash
docker compose exec app php artisan test
docker compose exec app ./vendor/bin/pint --test
docker compose exec node npm run check
docker compose exec app php artisan xibo:test-connection
docker compose exec app php artisan xibo:sync-displays
```

## Configuración Xibo

Configura estas variables en `.env` antes de sincronizar:

```env
XIBO_CMS_URL=https://cms.example.com
XIBO_BASE_URL="${XIBO_CMS_URL}/api"
XIBO_CLIENT_ID=
XIBO_CLIENT_SECRET=
```

En producción usa secretos externos al repositorio, `APP_DEBUG=false`, cookies seguras y un único scheduler (`php artisan schedule:work` o cron).
