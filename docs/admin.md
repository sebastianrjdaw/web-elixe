# Admin

MVP admin priorities:

- Login interno en `/admin/login` con usuarios de la tabla `users`.
- Dashboard en `/admin` con leads nuevos, leads semanales, pantallas visibles, ultima sincronizacion y pantallas incompletas.
- Leads en `/admin/leads`: listado, filtros base por query string, detalle, cambio de estado, reenvio de email interno y export CSV.
- Pantallas en `/admin/screens`: listado, avisos de datos incompletos, override local de visibilidad y boton manual "Sincronizar ahora".
- Logs de sincronizacion en `/admin/sync-runs`.
- Contenido editable en `/admin/content`.
- FAQs en `/admin/faqs`.
- Textos legales en `/admin/legal-pages`.
- Configuracion de contacto y email receptor en `/admin/settings`.
- Diagnosticos en `/admin/diagnostics`: comprueba entorno, Xibo y bloqueos del mapa publico con historial.
- Auditoria en `audit_logs` para cambios de estado, reenvios, export CSV, override de pantallas y sync manual.

Usuario inicial:

```bash
docker compose exec app php artisan elixe:create-admin admin@elixe.es
```

En desarrollo se puede sembrar el contenido inicial con:

```bash
docker compose exec app php artisan db:seed
```

Diagnosticos desde CLI:

```bash
./scripts/diagnose.sh
./scripts/diagnose.sh --json
```

Si una pantalla no aparece en el mapa, revisa `/admin/diagnostics` o la columna "Avisos" de `/admin/screens`. Para que una pantalla sea publica necesita coordenadas, `loc_tipo`, `loc_sector`, `web_visible=true`, `com_estado=disponible` y no tener override local que la oculte.

Planned CMS/admin areas:

- UI de auditoria.
- Users.
- Audit logs UI.

Pendiente para una fase posterior: valorar migrar a Filament o mantener el admin Inertia propio segun velocidad, licencias visuales y necesidades de CMS.
