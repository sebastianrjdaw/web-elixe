# Database

Tablas MVP existentes:

- `screens`: pantallas sincronizadas desde Xibo con datos publicos limpios.
- `screen_tags`: tags sincronizados relevantes.
- `leads`: solicitudes de locales, anunciantes u otras consultas.
- `lead_screen`: pantallas seleccionadas por anunciantes.
- `sync_runs`: ejecuciones de sincronizacion Xibo.
- `users`: usuarios de administracion.

Campos publicos de pantalla:

- `public_name`
- `municipality`
- `province`
- `latitude`
- `longitude`
- `location_type`
- `location_sector`
- `commercial_status`

Campos internos que no deben exponerse:

- `xibo_display_id`
- `public_code`
- `display_name`
- `address`
- datos tecnicos de red, licencia, payload Xibo o identificadores internos.
