# Architecture

Stack base:

- Laravel como backend unico.
- React + Inertia para la web publica.
- MySQL para datos propios.
- Redis para cache y colas.
- Laravel HTTP Client para Xibo.
- Laravel Mail + Queue para emails.
- Docker Compose para desarrollo.

Flujo general:

```text
Usuario publico
  -> Laravel + Inertia + React
  -> Base de datos propia
  -> Admin / CRM / CMS
  -> Xibo API solo lectura
```

Reglas importantes:

- El frontend nunca llama a Xibo.
- Las credenciales de Xibo solo viven en `.env`.
- Xibo es fuente de lectura, no base de datos publica.
- La web publica solo expone datos comerciales limpios.
- Los datos principales de pantallas se corrigen en Xibo y se vuelven a sincronizar.

Base funcional actual:

- Admin Inertia protegido por sesion Laravel.
- CRM de leads con estados, reenvio, CSV y auditoria.
- Pantallas con override local, avisos y sincronizacion manual.
- CMS sencillo para bloques de landing ES/GL, FAQs, textos legales y configuracion.
- Seeders para contenido inicial de desarrollo.
- Tests de formulario, exposicion publica segura, auditoria admin y CMS base.
