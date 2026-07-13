# Xibo Integration

Servicio principal: `App\Services\Xibo\XiboService`.

Autenticacion:

- OAuth `client_credentials`.
- Token cacheado hasta expiracion.
- Timeout configurable con `XIBO_TIMEOUT`.

Variables:

```env
XIBO_CMS_URL=https://cms.elixe.es
XIBO_BASE_URL="${XIBO_CMS_URL}/api"
XIBO_CLIENT_ID=
XIBO_CLIENT_SECRET=
XIBO_TIMEOUT=20
```

Comandos:

```bash
php artisan xibo:test-connection
php artisan xibo:sync-displays
```

`xibo:test-connection` valida OAuth, `/about` y `/clock`. Los errores de autorizacion no imprimen el cuerpo completo de la respuesta para evitar que un CMS externo pueda devolver datos sensibles en logs o consola.

Tags MVP:

- `loc_tipo`
- `loc_sector`
- `web_visible`
- `com_estado`

Regla publica:

```text
web_visible_from_xibo = true
commercial_status = disponible
latitude y longitude no vacios
sin override local que oculte
```

En Xibo, el tag `web_visible` debe existir y tener valor `true`. Si falta el tag, la pantalla queda oculta y el admin lo muestra como bloqueo.

La sincronizacion normaliza tags con `trim()` y no debe persistir payloads tecnicos innecesarios.
